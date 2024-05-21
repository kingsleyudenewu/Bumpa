<?php

namespace App\Actions;

use App\Http\Requests\InitiateBankTransferRequest;
use App\Service\LedgerService;
use Illuminate\Support\Facades\DB;

class ProcessBankTransfer
{
    public function execute(InitiateBankTransferRequest $request)
    {
        return DB::transaction(function () use ($request) {
            return resolve(LedgerService::class)->deductBankTransfer(
                auth()->user()->id,
                $request->amount,
                $request->reference
            );
        });
    }
}
