<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BankResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => data_get($this->name, 'name'),
            'code' => data_get($this->code, 'code'),
            'currency' => data_get($this->country->currency, 'currency'),
            'is_active' => data_get($this->is_active, 'active'),
        ];
    }
}
