<?php

namespace App\Clients;

use Exception;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Paystack
{
    public function __construct()
    {
        $this->client = Http::withToken('secret_key')->baseUrl('https://api.paystack.co');
    }

    /**
     * Initialize a transaction
     *
     * @param array $data
     * @return mixed
     * @throws HttpException
     */
    public function initializeTransaction(array $data): mixed
    {
        try {
            return $this->client->post('/transaction/initialize', $data)
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
