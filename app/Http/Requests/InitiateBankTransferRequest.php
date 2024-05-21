<?php

namespace App\Http\Requests;

use App\Enums\BookEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class InitiateBankTransferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => ['required', 'integer', 'min:1000', 'max:1000000'],
            'recipient' => ['required', 'string', 'exists:user_bank_accounts,recipient_code'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'source' => 'balance',
            'reason' => 'Transfer to bank account',
        ]);
    }

    protected function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            if ($this->user()->userBalance(BookEnum::CUSTOMER->value) < $this->amount) {
                $validator->errors()->add('amount', 'Insufficient balance');
            }

            if ($this->user()->bankAccounts()->where('recipient_code', $this->recipient)->doesntExist()) {
                $validator->errors()->add('recipient', 'Recipient bank account not found');
            }
        });
    }
}
