<?php

namespace App\Http\Controllers;

use App\Enums\BookEnum;
use App\Factories\PaymentFactory;
use App\Http\Requests\FundWalletRequest;
use App\Http\Requests\WalletTransferRequest;
use App\Service\LedgerService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    /**
     * This is a method that will return the balance of the authenticated user.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWalletsBalance(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        return $this->successResponse('Account balance retrieved successfully.', [
            'balance' => $user->userBalance(BookEnum::CUSTOMER),
        ]);
    }

    /**
     * This is a method that will transfer funds from the authenticated user to another user.
     *
     * @param WalletTransferRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function walletTransfer(WalletTransferRequest $request): \Illuminate\Http\JsonResponse
    {
        $transfer = resolve(LedgerService::class)->walletToWalletTransfer($request->user(), $request->beneficiary, $request->amount);

        return $transfer ? $this->successResponse('Transfer successful') : $this->badRequestAlert('Transfer failed');
    }

    /**
     * This is a method that will fund the wallet of the authenticated user.
     *
     * @param FundWalletRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function fundWallet(FundWalletRequest $request): \Illuminate\Http\JsonResponse
    {
        $paymentService = PaymentFactory::create($request->provider);

        $success = $paymentService->initializeTransaction($request->validated());

        return $this->successResponse('Transaction initialized successfully', $success);
    }

    public function verifyTransaction(string $reference)
    {
        $paymentService = PaymentFactory::create('paystack');

        $response = $paymentService->verifyTransaction($reference);

        return $response;
    }
}
