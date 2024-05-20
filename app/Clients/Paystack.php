<?php

namespace App\Clients;

use App\Contracts\PaymentClient;
use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Paystack extends PaymentClient
{
    protected PendingRequest $client;

    public function __construct()
    {
        $this->client = Http::withToken('secret_key')->baseUrl('https://api.paystack.co');
    }

    /**
     * Initialize a transaction
     *
     * @param array $payload
     * @return mixed
     * @throws HttpException
     */
    public function initializeTransaction(array $payload): mixed
    {
        try {
            return $this->client->post('/transaction/initialize', $payload)
                ->throw()
                ->json();
        } catch (Exception $exception) {
            throw new HttpException(Response::HTTP_SERVICE_UNAVAILABLE, $exception->getMessage());
        }
    }

    /**
     * Verify a transaction
     *
     * @param string $reference
     * @return mixed
     * @throws HttpException
     */
    public function verifyTransaction(string $reference): mixed
    {
        try {
            return $this->client->get("/transaction/verify/{$reference}")
                ->throw()
                ->json();
        } catch (Exception $exception) {
            throw new HttpException(Response::HTTP_SERVICE_UNAVAILABLE, $exception->getMessage());
        }
    }
}
