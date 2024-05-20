<?php

namespace App\Logics;

use App\Contracts\WebhookProcessor;
use App\Enums\BookEnum;
use App\Enums\ProviderEnum;
use App\Jobs\ProcessPaystackWebhook;
use App\Models\WebhookLog;
use Illuminate\Http\Request;

class PaystackProcessor extends WebhookProcessor
{
    public function __construct(protected Request $request)
    {
    }

    public function createWebhookLog(): WebhookLog
    {
        WebhookLog::add(ProviderEnum::PAYSTACK->value, $this->request, $this->request->input('data.metadata.tx_ref'));
    }

    public function shouldProcessRequest(): bool
    {
        return $this->request->input('event') === 'charge.success' &&
            $this->request->input('data.status') === 'success';
    }

    public function dispatchWebhookToJob()
    {
        ProcessPaystackWebhook::dispatch($this->request->all());
    }
}
