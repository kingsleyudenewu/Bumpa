<?php

namespace App\Clients;

use App\Contracts\PaymentClient;
use App\Enums\ProviderEnum;
use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Flutterwave extends PaymentClient
{
    protected PendingRequest $client;

    public function __construct()
    {
        $this->client = Http::withToken(config('flutterwave.secret_key'))->baseUrl(config('flutterwave.payment_url'));
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
        $payload = $this->buildPayload($data);

        try {
            $response = $this->client->post('/transaction/initialize', $payload)
                ->throw()
                ->json();

            if (Arr::get($response, 'status') !== 'success') {
                abort(Response::HTTP_BAD_REQUEST, Arr::get($response, 'message'));
            }

            return Arr::get($response, 'data.link');

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
            $response = $this->client->get("/transactions/{$reference}/verify")
                ->throw()
                ->json();

            if (Arr::get($response, 'status') !== 'success') {
                abort(Response::HTTP_BAD_REQUEST, Arr::get($response, 'message'));
            }

            return Arr::get($response, 'data');

        } catch (Exception $exception) {
            throw new HttpException(Response::HTTP_SERVICE_UNAVAILABLE, $exception->getMessage());
        }
    }

    /**
     * Build the payload for the transaction
     *
     * @param array $data
     * @return array
     */
    private function buildPayload(array $data): array
    {
        return [
            'tx_ref' => Str::uuid()->toString(),
            'amount' => $data['amount'],
            'customer' => [
                'email' => auth()->user()->email,
            ],
            'meta' => [
                'user_ref' => auth()->user()->code,
                'provider' => ProviderEnum::FLUTTERWAVE->value
            ]
        ];
    }
}
