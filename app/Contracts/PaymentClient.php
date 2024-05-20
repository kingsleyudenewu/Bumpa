<?php

namespace App\Contracts;

abstract class PaymentClient
{
    /**
     * Initialize a transaction.
     *
     * @param array $payload
     *
     * @return mixed
     */
    abstract public function initializeTransaction(array $payload): mixed;

    /**
     * Verify a transaction.
     *
     * @param string $reference
     *
     * @return mixed
     */
    abstract public function verifyTransaction(string $reference): mixed;
}
