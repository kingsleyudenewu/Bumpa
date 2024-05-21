<?php

namespace App\Http\Controllers;

use App\Actions\CreateBankAccount;
use App\Clients\Paystack;
use App\Http\Requests\CreateRecipientAccountRequest;
use App\Http\Requests\InitiateBankTransferRequest;
use App\Http\Requests\VerifyBankAccountRequest;
use App\Http\Resources\BankAccountResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WithdrawalController extends Controller
{
    /**
     * Get all banks.
     */
    public function getBanks(): \Illuminate\Http\JsonResponse
    {
        $banks = Cache::remember('users', now()->addHour(), function () {
            return (new Paystack())->fetchAllBanks();
        });

        return $this->successResponse('success', $banks);
    }

    /**
     * Get all bank accounts.
     */
    public function getAllBankAccounts(): \Illuminate\Http\JsonResponse
    {
        $bankAccounts = auth()->user()->bankAccounts()->get();

        return $this->successResponse('success', BankAccountResource::collection($bankAccounts));
    }

    /**
     * Verify bank account.
     */
    public function verifyBankAccount(VerifyBankAccountRequest $request): \Illuminate\Http\JsonResponse
    {
        $response = (new Paystack())->verifyAccountNumber($request->validated());

        return $this->successResponse('success', $response);
    }

    /**
     * Create a recipient account.
     */
    public function createRecipient(CreateRecipientAccountRequest $request): \Illuminate\Http\JsonResponse
    {
        $bankDetails = (new CreateBankAccount())->execute($request);

        return $this->successResponse('success', new BankAccountResource($bankDetails));
    }

    /**
     * Initiate a bank transfer.
     */
    public function initiateTransfer(InitiateBankTransferRequest $request): \Illuminate\Http\JsonResponse
    {
        $response = (new Paystack())->initiateTransfer($request->validated());

        return $this->successResponse('success', $response);
    }
}
