<?php

namespace App\Http\Requests;

use App\Enums\BookEnum;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class WalletTransferRequest extends FormRequest
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
            'amount' => 'required',
            'email' => ['required', 'exists:users,email'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $balance = $this->user()->userBalance(BookEnum::CUSTOMER);

        if ((float) $balance < (float) $this->amount) {
            abort(Response::HTTP_BAD_REQUEST, "Insufficient balance in your wallet");
        }

        $beneficiary = User::whereEmail($this->email)->firstOrFail();

        if ($this->user()->id === $beneficiary->id) {
            abort(Response::HTTP_BAD_REQUEST, "Sorry you cannot transfer to yourself");
        }

        $this->merge([
            'beneficiary' => $beneficiary,
        ]);
    }
}
