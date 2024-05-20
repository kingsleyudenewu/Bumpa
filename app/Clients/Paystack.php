<?php

namespace App\Clients;

use App\Contracts\PaymentClient;
use App\Enums\ProviderEnum;
use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
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
        $payload = [
            'email' => auth()->user()->email,
            'amount' => $payload['amount'] * 100,
            'metadata' => [
                'user_ref' => auth()->user()->code,
                'provider' => ProviderEnum::PAYSTACK
            ]
        ];

        try {
            $response = $this->client->post('/transaction/initialize', $payload)
                ->throw()
                ->json();

            if (!Arr::get($response, 'status', false)) {
                abort(Response::HTTP_BAD_REQUEST, Arr::get($response, 'message'));
            }

            return Arr::get($response, 'data.authorization_url');

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
            $response =  $this->client->get("/transaction/verify/{$reference}")
                ->throw()
                ->json();

            if (!Arr::get($response, 'status', false)) {
                abort(Response::HTTP_BAD_REQUEST, Arr::get($response, 'message'));
            }

            return Arr::get($response, 'data');

        } catch (Exception $exception) {
            throw new HttpException(Response::HTTP_SERVICE_UNAVAILABLE, $exception->getMessage());
        }
    }
}
