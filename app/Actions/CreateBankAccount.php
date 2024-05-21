<?php

namespace App\Actions;

use App\Clients\Paystack;
use App\Http\Requests\CreateRecipientAccountRequest;
use App\Models\UserBankAccount;

class CreateBankAccount
{
    public function execute(CreateRecipientAccountRequest $request)
    {
        $response = (new Paystack())->createRecipient($request->validated());

        return UserBankAccount::create([
            'user_id' => auth()->id(),
            'recipient_code' => data_get($response,'recipient_code'),
            'account_number' => data_get($response,'details.account_number'),
            'bank_code' => data_get($response,'details.bank_code'),
            'bank_name' => data_get($response,'details.bank_name'),
            'account_name' => data_get($response,'details.account_name'),
        ]);
    }
}
