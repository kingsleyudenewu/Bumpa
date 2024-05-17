<?php

namespace App\Traits;

use Exception;
use HttpException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as StatusResponse;

trait HasApiResponse
{
    /**
     * Set the server error response.
     *
     * @param $message
     * @param Exception|null $exception
     *
     * @return JsonResponse
     */
    public function serverErrorAlert($message, Exception $exception = null, $code = null): JsonResponse
    {
        if (null !== $exception) {
            Log::error("{$exception->getMessage()} on line {$exception->getLine()} in {$exception->getFile()}");
            report($exception);
        }

        $response = ["status" => "error", 'message' => $message];

        if (null !== $exception) {
            $response['exception'] = $exception->getMessage();
            Log::error($exception->getMessage());
        }

        if (null !== $code) {
            return Response::json($response, $code);
        }

        return Response::json($response, 500);
    }

    /**
     * Set the server error response.
     *
     * @param $message
     * @param HttpException $exception
     *
     * @return JsonResponse
     */
    public function httpErrorAlert($message, HttpException $exception = null): JsonResponse
    {
        if (null !== $exception) {
            Log::error("{$exception->getMessage()} on line {$exception->getLine()} in {$exception->getFile()}");
        }

        $response = ["status" => "error", 'message' => $message];

        if (null !== $exception) {
            $response['exception'] = $exception->getMessage();
        }

        return Response::json($response, 400);
    }

    /**
     * Set the form validation error response.
     *
     * @param $errors
     * @param $data
     *
     * @return JsonResponse
     */
    public function formValidationErrorAlert($data = null): JsonResponse
    {
        return $this->toJsonResponse('Validation error occurred.', StatusResponse::HTTP_UNPROCESSABLE_ENTITY, $data);
    }

    /**
     * Set the "not found" error response.
     *
     * @param $message
     * @param null $data
     *
     * @return JsonResponse
     */
    public function notFoundAlert($message, $data = null): JsonResponse
    {
        return $this->toJsonResponse($message, StatusResponse::HTTP_NOT_FOUND, $data);
    }

    /**
     * Set bad request error response.
     *
     * @param string $message
     * @param null $data
     *
     * @return JsonResponse
     */
    public function badRequestAlert(string $message, $data = null): JsonResponse
    {
        return $this->toJsonResponse($message, 400, $data);
    }

    /**
     * Set the success response alert.
     *
     * @param $message
     * @param $data
     *
     * @return JsonResponse
     */
    public function successResponse($message, $data = null): JsonResponse
    {
        return $this->toJsonResponse($message, StatusResponse::HTTP_OK, $data);
    }

    /**
     * Set the created resource response alert.
     *
     * @param $message
     * @param $data
     *
     * @return JsonResponse
     */
    public function createdResponse($message, $data = null): JsonResponse
    {
        return $this->toJsonResponse($message, StatusResponse::HTTP_CREATED, $data);
    }

    /**
     * Set forbidden request error response.
     *
     * @param $message
     * @param $data
     *
     * @return JsonResponse
     */
    public function forbiddenRequestAlert($message, $code = StatusResponse::HTTP_FORBIDDEN, $data = null): JsonResponse
    {
        return $this->toJsonResponse($message, $code, $data);
    }

    public function toJsonResponse(string $message, int $status, $data = null): JsonResponse
    {
        $isSuccessful = $status >= 100 && $status < 400;

        $response = ["status" => $isSuccessful ? 'success' : 'error', 'message' => $message];

        if (!empty($data)) {
            $response[$isSuccessful ? 'data' : 'error'] = $data;
        }

        if ($data instanceof JsonResponse) {
            $data = $data->getData(true);

            $response['data'] = Arr::get($data, 'data');
            $response['meta'] = Arr::get($data, 'meta');
            $response['links'] = Arr::get($data, 'links');
        }

        return Response::json($response, $status);
    }
}
