<?php

namespace App\Clients;

use App\Contracts\PaymentClient;
use App\Enums\ProviderEnum;
use App\Http\Resources\BankResource;
use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Paystack extends PaymentClient
{
    protected PendingRequest $client;

    public function __construct()
    {
        $this->client = Http::withToken(config('paystack.secret_key'))->baseUrl(config('paystack.payment_url'));
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
                'provider' => ProviderEnum::PAYSTACK->value,
                'tx_ref' => Str::uuid()->toString(),
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
            return $this->processGetRequest("/transaction/verify/{$reference}");

        } catch (Exception $exception) {
            throw new HttpException(Response::HTTP_SERVICE_UNAVAILABLE, $exception->getMessage());
        }
    }

    public function fetchAllBanks(): array
    {
        try {
            $banks = $this->processGetRequest('/bank?country=nigeria');

            return collect($banks)->mapInto(BankResource::class)->all();

        } catch (Exception $exception) {
            throw new HttpException(Response::HTTP_SERVICE_UNAVAILABLE, $exception->getMessage());
        }
    }

    public function verifyAccountNumber(array $query)
    {
        try {
            return $this->processGetRequest('/bank/resolve', $query);

        } catch (Exception $exception) {
            throw new HttpException(Response::HTTP_SERVICE_UNAVAILABLE, $exception->getMessage());
        }
    }

    public function createRecipient(array $data)
    {
        try {
            return $this->processPostRequest('/transferrecipient', $data);

        } catch (Exception $exception) {
            throw new HttpException(Response::HTTP_SERVICE_UNAVAILABLE, $exception->getMessage());
        }
    }

    public function initiateTransfer(array $data)
    {
        try {
            return $this->processPostRequest('/transfer', $data);

        } catch (Exception $exception) {
            throw new HttpException(Response::HTTP_SERVICE_UNAVAILABLE, $exception->getMessage());
        }
    }

    private function processPostRequest(string $uri, array $payload)
    {
        $response = $this->client->post($uri, $payload)
            ->throw()
            ->json();

        if (!Arr::get($response, 'status', false)) {
            abort(Response::HTTP_BAD_REQUEST, Arr::get($response, 'message'));
        }

        return Arr::get($response, 'data');
    }

    private function processGetRequest(string $uri, array $queryParam = [])
    {
        $response = $this->client->get($uri, $queryParam)
            ->throw()
            ->json();

        if (!Arr::get($response, 'status', false)) {
            abort(Response::HTTP_BAD_REQUEST, Arr::get($response, 'message'));
        }

        return Arr::get($response, 'data');
    }
}
