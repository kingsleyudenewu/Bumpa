<?php

namespace App\Contracts;

use App\Models\WebhookLog;

abstract class WebhookProcessor
{
    /**
     * Create webhook log
     *
     * @return WebhookLog
     */
    abstract public function createWebhookLog(): WebhookLog;

    /**
     * Determine whether webhook request should be processed or not
     *
     * @return bool
     */
    abstract public function shouldProcessRequest(): bool;

    /**
     * Dispatch webhook to a job to complete
     *
     * @return void
     */
    abstract public function dispatchWebhookToJob();

    /**
     * Execute the webhook
     *
     * @return void
     */
    public function execute()
    {
        $this->createWebhookLog();

        if (! $this->shouldProcessRequest()) {
            return;
        }

        $this->dispatchWebhookToJob();
    }

}
