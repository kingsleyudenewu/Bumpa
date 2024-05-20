<?php

namespace App\Enums;

enum ProviderEnum: string
{
    case PAYSTACK = 'paystack';
    case FLUTTERWAVE = 'flutterwave';
}
