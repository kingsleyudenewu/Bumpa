<?php

namespace App\Http\Controllers;

class TransactionController extends Controller
{
    public function getTransactions()
    {
        return $this->successResponse('Wallet history successful.', auth()->user()->transactionHistory());
    }
}
