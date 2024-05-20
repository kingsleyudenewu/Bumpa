<?php

namespace App\Http\Controllers;

use App\Logics\PaystackProcessor;
use Illuminate\Http\Request;

class PaystackWebhookController extends Controller
{
    /**
     * Handle Paystack webhook.
     */
    public function __invoke(Request $request): \Illuminate\Http\JsonResponse
    {
        (new PaystackProcessor($request))->execute();

        return $this->successResponse('Paystack webhook received');
    }
}
