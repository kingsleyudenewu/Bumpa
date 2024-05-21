<?php

namespace App\Jobs;

use App\Clients\Paystack;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessBankTransferJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public array $payload,
        public int $user_id
    )
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        (new Paystack())->initiateTransfer($this->payload);
    }
}
