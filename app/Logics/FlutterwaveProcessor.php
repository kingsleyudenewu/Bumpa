<?php

namespace App\Logics;

use App\Contracts\WebhookProcessor;
use App\Enums\ProviderEnum;
use App\Jobs\ProcessFlutterwaveWebhook;
use App\Models\WebhookLog;
use Illuminate\Http\Request;

class FlutterwaveProcessor extends WebhookProcessor
{
    public function __construct(protected Request $request)
    {
    }

    public function createWebhookLog(): WebhookLog
    {
        WebhookLog::add(ProviderEnum::FLUTTERWAVE->value, $this->request, $this->request->input('data.meta.tx_ref'));
    }

    public function shouldProcessRequest(): bool
    {
        return $this->request->input('event') === 'charge.completed' &&
            $this->request->input('data.status') === 'successful';
    }

    public function dispatchWebhookToJob()
    {
        ProcessFlutterwaveWebhook::dispatch($this->request->all());
    }
}
