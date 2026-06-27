<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the RouteServiceProvider and are prefixed with
| "/api". They do NOT have CSRF middleware, making them safe for external
| services (like Safaricom) to POST to.
|
*/

// M-Pesa STK Push Callback — Safaricom POSTs here after payment
Route::post('/mpesa/callback', [PaymentController::class, 'callback']);
