<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $transactionAmount = number_format(-1*$this->tx_amount,2);

        return [
            'tx_book_id' => $this->tx_book_id,
            'tx_date' => $this->tx_date,
            'tx_value_date' => $this->tx_value_date,
            'tx_amount' => $transactionAmount,
            'tx_type' => $transactionAmount < 0 ? 'Debit' : 'Credit',
            'tx_remarks' => $this->tx_remarks,
            'tx_code' => $this->tx_code,
        ];
    }
}
