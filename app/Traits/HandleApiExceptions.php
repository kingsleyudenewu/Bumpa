<?php

namespace App\Traits;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

trait HandleApiExceptions
{
    use HasApiResponse;

    private function buildResponse(Throwable $e, Request $request)
    {
        if ($e instanceof ValidationException) {
            return $this->formValidationErrorAlert(Arr::flatten($e->errors()));
        }

        if ($e instanceof ModelNotFoundException) {
            return $this->notFoundAlert('Model cannot be found');
        }

        if ($e instanceof NotFoundHttpException) {
            return response()->json(['message' => $e->getMessage() || "Resource cannot be found"],
                404);
        }

        if ($e instanceof HttpResponseException) {
            return $e->getResponse();
        }

        if ($e instanceof HttpException) {
            return $this->httpErrorAlert('Http Error', $e);
        }

        if ($e instanceof HttpClientException) {
            return $this->httpErrorAlert($e->getMessage());
        }
        return $this->serverErrorAlert($e->getMessage());
    }



    /**
     * Prepare exception for rendering.
     *
     * @param \Throwable $exception
     * @param \Illuminate\Http\Request $request
     *
     * @return Throwable
     */
    protected function prepareApiException(Throwable $exception, $request): Throwable
    {
        if ($exception instanceof NotFoundHttpException) {
            $message = with($exception->getMessage(), function ($message) {
                return blank($message) || Str::contains($message, 'No query results for model')
                    ? 'Resource not found.' : $message;
            });

            $exception = new HttpException(404, $message, $exception);
        } elseif ($exception instanceof ValidationException) {
            $exception = new ValidationResponseException($exception->validator, $request);
        } elseif ($exception instanceof AuthenticationException) {
            $exception = new HttpException(401, $exception->getMessage(), $exception);
        } elseif ($exception instanceof UnauthorizedException) {
            $exception = new HttpException(403, $exception->getMessage(), $exception);
        }

        return $exception;
    }

    /**
     * Create a response data array based on exception.
     *
     * @param Throwable $exception
     *
     * @return array
     */
    protected function composeResponseDataFromException(Throwable $exception): array
    {
        $statusCode = $this->isHttpException($exception) ?
            $exception->getCode() :
            Response::HTTP_INTERNAL_SERVER_ERROR;

        $responseData = [
            'status' => 'error',
            'message' => $exception->getMessage(),
        ];

        if (config('app.debug') && ! $this->isHttpException($exception)) {
            $responseData = $this->appendDebugData($responseData, $exception);
        }

        return $responseData;
    }

    /**
     * Append debug data to the response data returned.
     *
     * @param array $responseData
     * @param Throwable $exception
     *
     * @return array
     */
    protected function appendDebugData(array $responseData, Throwable $exception): array
    {
        $responseData['_debug'] = [
            'message' => $exception->getMessage(),
            'exception' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => collect($exception->getTrace())->map(fn($trace) => Arr::except($trace, ['args']))->all(),
        ];

        return $responseData;
    }

    protected function isHttpException(Throwable $e): bool
    {
        return $e instanceof HttpExceptionInterface;
    }
}
