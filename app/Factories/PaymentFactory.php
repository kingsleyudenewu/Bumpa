<?php

namespace App\Factories;

use App\Clients\Flutterwave;
use App\Clients\Paystack;
use InvalidArgumentException;

class PaymentFactory
{
    /**
     * Create a new payment provider instance.
     *
     * @param string $provider
     * @return Paystack|Flutterwave
     */
    public static function create(string $provider): Paystack|Flutterwave
    {
        return match (strtolower($provider)) {
            'paypal' => new Paystack(),
            'stripe' => new Flutterwave(),
            default => throw new InvalidArgumentException("Unsupported payment provider: {$provider}")
        };
    }
}
