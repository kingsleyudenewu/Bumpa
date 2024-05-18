<?php

namespace App\Enums;

enum LedgerEnum: string
{
    case ACCOUNT_FUNDING = 'AccountFunding';
    case INCOME = 'IncomeLedger';
    case WITHDRAWAL = 'WithdrawalLedger';
}
