<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\AuditLog;
use App\Services\MpesaService;

class PaymentController extends Controller
{
    protected MpesaService $mpesa;

    public function __construct(MpesaService $mpesa)
    {
        $this->mpesa = $mpesa;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CHECKOUT PAGE — show the STK Push phone entry form
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Show the M-Pesa checkout page for a given enrollment.
     */
    public function show($enrollmentId)
    {
        $student = Auth::user()->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }

        $enrollment = Enrollment::with(['program.organization', 'payment'])
            ->where('student_id', $student->id)
            ->findOrFail($enrollmentId);

        // If already paid or free, bounce back
        $payment = $enrollment->payment;
        if (!$payment || $payment->status === 'paid') {
            return redirect()->route('student.enrollments.index')->with('info', 'This enrollment is already paid.');
        }

        return view('payment.checkout', compact('enrollment', 'payment'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // INITIATE — trigger Safaricom STK Push
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Initiate an M-Pesa STK Push for an enrollment.
     */
    public function initiate(Request $request, $enrollmentId)
    {
        $request->validate([
            'phone' => ['required', 'regex:/^(07|01|2547|2541)\d{8}$/'],
        ], [
            'phone.regex' => 'Please enter a valid Safaricom phone number (e.g. 0712345678 or 254712345678).',
        ]);

        $student = Auth::user()->student;

        if (!$student) {
            return back()->with('error', 'Student profile not found.');
        }

        $enrollment = Enrollment::with(['program', 'payment'])
            ->where('student_id', $student->id)
            ->findOrFail($enrollmentId);

        $payment = $enrollment->payment;

        if (!$payment || $payment->status === 'paid') {
            return redirect()->route('student.enrollments.index')->with('info', 'This enrollment is already paid.');
        }

        // Normalise phone number to 2547XXXXXXXX format
        $phone = $this->normalisePhone($request->phone);
        $amount = $payment->amount;
        $reference = 'SC-ENR-' . $enrollment->id;
        $description = 'SkillConnect: ' . $enrollment->program->name;

        $result = $this->mpesa->stkPush($phone, $amount, $reference, $description);

        if (!$result['success']) {
            return back()->with('error', $result['message'])->withInput();
        }

        // Store the checkout request details so we can match the callback later
        $payment->update([
            'checkout_request_id' => $result['CheckoutRequestID'],
            'merchant_request_id' => $result['MerchantRequestID'],
            'phone_number'        => $phone,
            'payment_method'      => 'M-Pesa',
        ]);

        AuditLog::log(Auth::id(), 'mpesa_stk_push_initiated', "STK Push initiated for enrollment #{$enrollment->id}, CheckoutRequestID: {$result['CheckoutRequestID']}");

        return redirect()->route('student.payment.pending', $enrollment->id)
            ->with('success', $result['CustomerMessage']);
    }

    /**
     * Show a "waiting for payment" page that polls for status updates.
     */
    public function pending($enrollmentId)
    {
        $student = Auth::user()->student;

        $enrollment = Enrollment::with(['program', 'payment'])
            ->where('student_id', $student->id)
            ->findOrFail($enrollmentId);

        return view('payment.pending', compact('enrollment'));
    }

    /**
     * AJAX endpoint the pending page can poll to check if payment was confirmed.
     */
    public function status($enrollmentId)
    {
        $student = Auth::user()->student;

        $enrollment = Enrollment::with('payment')
            ->where('student_id', $student->id)
            ->findOrFail($enrollmentId);

        $status = $enrollment->payment?->status ?? 'pending';

        return response()->json([
            'status'          => $status,
            'receipt'         => $enrollment->payment?->mpesa_receipt_number,
            'enrollment_status' => $enrollment->status,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CALLBACK — Safaricom sends this POST after the user completes payment
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Handle the M-Pesa STK Push callback from Safaricom.
     * This route is in api.php — no CSRF middleware.
     */
    public function callback(Request $request)
    {
        Log::info('M-Pesa Callback Received', $request->all());

        $body = $request->input('Body.stkCallback');

        if (!$body) {
            Log::warning('M-Pesa Callback: Missing Body.stkCallback');
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
        }

        $resultCode       = $body['ResultCode'] ?? -1;
        $checkoutRequestId = $body['CheckoutRequestID'] ?? null;

        if (!$checkoutRequestId) {
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
        }

        $payment = Payment::where('checkout_request_id', $checkoutRequestId)->first();

        if (!$payment) {
            Log::warning("M-Pesa Callback: Payment not found for CheckoutRequestID {$checkoutRequestId}");
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
        }

        DB::beginTransaction();
        try {
            if ($resultCode === 0) {
                // ✅ Payment successful
                $items = collect($body['CallbackMetadata']['Item'] ?? [])
                    ->keyBy('Name');

                $receipt = $items->get('MpesaReceiptNumber')['Value'] ?? null;
                $amount  = $items->get('Amount')['Value'] ?? $payment->amount;
                $phone   = $items->get('PhoneNumber')['Value'] ?? $payment->phone_number;

                $payment->update([
                    'status'               => 'paid',
                    'mpesa_receipt_number' => $receipt,
                    'transaction_reference'=> $receipt ?? 'MPESA-' . strtoupper(Str::random(8)),
                    'amount'               => $amount,
                    'phone_number'         => $phone,
                ]);

                // Auto-approve the enrollment
                if ($payment->enrollment) {
                    $payment->enrollment->update(['status' => 'approved']);
                }

                AuditLog::log(null, 'mpesa_payment_success', "M-Pesa payment confirmed: receipt {$receipt}, enrollment #{$payment->enrollment_id}");
            } else {
                // ❌ Payment failed/cancelled
                $payment->update(['status' => 'failed']);

                AuditLog::log(null, 'mpesa_payment_failed', "M-Pesa payment failed for CheckoutRequestID: {$checkoutRequestId}, ResultCode: {$resultCode}");
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('M-Pesa Callback: DB error', ['error' => $e->getMessage()]);
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Normalise phone number to the 2547XXXXXXXX format required by M-Pesa.
     */
    protected function normalisePhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '254' . substr($phone, 1);
        }

        if (!str_starts_with($phone, '254')) {
            $phone = '254' . $phone;
        }

        return $phone;
    }
}
