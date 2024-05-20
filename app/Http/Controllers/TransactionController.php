<?php

namespace App\Http\Controllers;

class TransactionController extends Controller
{
    /**
     * Get all wallet transactions.
     */
    public function getTransactions(): \Illuminate\Http\JsonResponse
    {
        return $this->successResponse('Wallet history successful.', auth()->user()->transactionHistory());
    }
}
