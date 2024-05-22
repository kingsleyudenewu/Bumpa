<?php

namespace App\Logics;

use App\Contracts\WebhookProcessor;
use App\Enums\BookEnum;
use App\Enums\ProviderEnum;
use App\Jobs\ProcessPaystackTransactionWebhook;
use App\Models\Tx;
use App\Models\WebhookLog;
use App\Service\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaystackProcessor extends WebhookProcessor
{
    public function __construct(protected Request $request)
    {
    }

    public function createWebhookLog()
    {
        WebhookLog::add(ProviderEnum::PAYSTACK->value, $this->request);
    }

    public function shouldProcessRequest(): bool
    {
        return !empty($this->request->input('event'));
    }

    public function dispatchWebhookToJob()
    {
        if ($this->request->input('event') === 'charge.success' &&
            $this->request->input('data.status') === 'success') {
            ProcessPaystackTransactionWebhook::dispatch($this->request->all());
        }

        if (in_array($this->request->input('event'), ['transfer.failed', 'transfer.reversed'])) {
            $this->processReversalFromWebhook();
        }
    }

    private function processReversalFromWebhook()
    {
        $transaction = Tx::with('book')
            ->whereHas('book', function ($query) {
                $query->where('book_type', BookEnum::CUSTOMER->value);
            })
            ->where('tx_code', $this->request->input('data.reference'))
            ->first();


        resolve(LedgerService::class)->deductBankTransfer(
            $transaction->book->book_src_id,
            $this->request->input('data.amount') / 100,
            Str::uuid()->toString(),
            true
        );
    }
}
