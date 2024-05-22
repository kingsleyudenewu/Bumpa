<?php

use App\Http\Controllers\FlutterwaveWebhookController;
use App\Http\Controllers\PaystackWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/flutterwave', [FlutterwaveWebhookController::class])->name('webhook.flutterwave');
Route::post('/paystack', [PaystackWebhookController::class])->name('webhook.paystack');
