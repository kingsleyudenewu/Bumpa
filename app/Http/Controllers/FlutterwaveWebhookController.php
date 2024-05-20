<?php

namespace App\Http\Controllers;

use App\Logics\FlutterwaveProcessor;
use Illuminate\Http\Request;

class FlutterwaveWebhookController extends Controller
{
    /**
     * Handle Paystack webhook.
     */
    public function __invoke(Request $request): \Illuminate\Http\JsonResponse
    {
        (new FlutterwaveProcessor($request))->execute();

        return $this->successResponse('Flutterwave webhook received');
    }
}
