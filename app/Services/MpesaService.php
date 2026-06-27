<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpesaService
{
    protected string $env;
    protected string $consumerKey;
    protected string $consumerSecret;
    protected string $shortcode;
    protected string $passkey;
    protected string $callbackUrl;

    public function __construct()
    {
        $this->env            = config('services.mpesa.env', 'sandbox');
        $this->consumerKey    = config('services.mpesa.consumer_key');
        $this->consumerSecret = config('services.mpesa.consumer_secret');
        $this->shortcode      = config('services.mpesa.shortcode');
        $this->passkey        = config('services.mpesa.passkey');
        $this->callbackUrl    = config('services.mpesa.callback_url');
    }

    /**
     * Get the Daraja base URL based on environment.
     */
    protected function baseUrl(): string
    {
        return $this->env === 'production'
            ? 'https://api.safaricom.co.ke'
            : 'https://sandbox.safaricom.co.ke';
    }

    /**
     * Generate an OAuth 2.0 access token from Safaricom Daraja API.
     */
    public function getAccessToken(): string|null
    {
        $credentials = base64_encode("{$this->consumerKey}:{$this->consumerSecret}");

        try {
            $response = Http::withHeaders([
                'Authorization' => "Basic {$credentials}",
            ])->get("{$this->baseUrl()}/oauth/v1/generate", [
                'grant_type' => 'client_credentials',
            ]);

            if ($response->successful()) {
                return $response->json('access_token');
            }

            Log::error('M-Pesa: Failed to get access token', ['response' => $response->body()]);
            return null;
        } catch (\Exception $e) {
            Log::error('M-Pesa: Exception during access token fetch', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Initiate an STK Push (Lipa Na M-Pesa Online).
     *
     * @param string $phone    Phone in format 2547XXXXXXXX
     * @param float  $amount   Amount to charge (KES)
     * @param string $reference Short reference e.g. "SC-ENR-123"
     * @param string $description Human-readable description
     */
    public function stkPush(string $phone, float $amount, string $reference, string $description = 'SkillConnect Payment'): array
    {
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            return ['success' => false, 'message' => 'Could not obtain M-Pesa access token. Please try again.'];
        }

        $timestamp = now()->format('YmdHis');
        $password  = base64_encode("{$this->shortcode}{$this->passkey}{$timestamp}");

        $payload = [
            'BusinessShortCode' => $this->shortcode,
            'Password'          => $password,
            'Timestamp'         => $timestamp,
            'TransactionType'   => 'CustomerPayBillOnline',
            'Amount'            => (int) ceil($amount),
            'PartyA'            => $phone,
            'PartyB'            => $this->shortcode,
            'PhoneNumber'       => $phone,
            'CallBackURL'       => $this->callbackUrl,
            'AccountReference'  => $reference,
            'TransactionDesc'   => $description,
        ];

        try {
            $response = Http::withToken($accessToken)
                ->post("{$this->baseUrl()}/mpesa/stkpush/v1/processrequest", $payload);

            $data = $response->json();

            Log::info('M-Pesa STK Push Response', $data ?? []);

            if ($response->successful() && isset($data['CheckoutRequestID'])) {
                return [
                    'success'              => true,
                    'CheckoutRequestID'    => $data['CheckoutRequestID'],
                    'MerchantRequestID'    => $data['MerchantRequestID'],
                    'CustomerMessage'      => $data['CustomerMessage'] ?? 'Please check your phone for the M-Pesa prompt.',
                ];
            }

            return [
                'success' => false,
                'message' => $data['errorMessage'] ?? $data['ResponseDescription'] ?? 'STK Push failed. Please try again.',
            ];
        } catch (\Exception $e) {
            Log::error('M-Pesa: Exception during STK Push', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'An error occurred. Please try again.'];
        }
    }
}
