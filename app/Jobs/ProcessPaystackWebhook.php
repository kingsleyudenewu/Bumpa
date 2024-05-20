<?php

namespace App\Jobs;

use App\Enums\BookEnum;
use App\Models\User;
use App\Service\LedgerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

class ProcessPaystackWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public array $payload
    ){}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        resolve(LedgerService::class)->accountFundingLedger(
            (Arr::get($this->payload, 'amount') / 100),
            Arr::get($this->payload, 'data.metadata.tx_ref'),
            User::where('email', Arr::get($this->payload, 'data.customer.email'))->firstOrFail()->id,
            BookEnum::CUSTOMER->value
        );
    }
}
