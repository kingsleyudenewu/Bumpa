<?php

namespace App\Http\Controllers;

use App\Enums\BookEnum;
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
}
