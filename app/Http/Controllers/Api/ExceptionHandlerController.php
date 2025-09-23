<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\BusinessLogicException;
use App\Exceptions\ValidationException;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\UnauthorizedAccessException;
use App\Services\LoggingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException as LaravelValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class ExceptionHandlerController
{
    public function __construct(
        private LoggingService $loggingService
    ) {}

    public function handle(Throwable $exception, Request $request): JsonResponse
    {
        // Логируем все исключения
        $this->logException($exception, $request);

        // Обрабатываем различные типы исключений
        if ($exception instanceof BusinessLogicException) {
            return $this->handleBusinessLogicException($exception);
        }

        if ($exception instanceof ValidationException) {
            return $this->handleValidationException($exception);
        }

        if ($exception instanceof ResourceNotFoundException) {
            return $this->handleResourceNotFoundException($exception);
        }

        if ($exception instanceof UnauthorizedAccessException) {
            return $this->handleUnauthorizedAccessException($exception);
        }

        if ($exception instanceof LaravelValidationException) {
            return $this->handleLaravelValidationException($exception);
        }

        if ($exception instanceof HttpException) {
            return $this->handleHttpException($exception);
        }

        // Обрабатываем общие исключения
        return $this->handleGenericException($exception);
    }

    private function handleBusinessLogicException(BusinessLogicException $exception): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error_code' => $exception->getErrorCode(),
            'message' => $exception->getMessage(),
            'context' => $exception->getContext(),
            'timestamp' => now()->toISOString()
        ], $exception->getCode());
    }

    private function handleValidationException(ValidationException $exception): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $exception->getMessage(),
            'errors' => $exception->getErrors(),
            'data' => $exception->getData(),
            'timestamp' => now()->toISOString()
        ], $exception->getCode());
    }

    private function handleResourceNotFoundException(ResourceNotFoundException $exception): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $exception->getMessage(),
            'resource_type' => $exception->getResourceType(),
            'resource_id' => $exception->getResourceId(),
            'timestamp' => now()->toISOString()
        ], $exception->getCode());
    }

    private function handleUnauthorizedAccessException(UnauthorizedAccessException $exception): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $exception->getMessage(),
            'action' => $exception->getAction(),
            'resource' => $exception->getResource(),
            'context' => $exception->getContext(),
            'timestamp' => now()->toISOString()
        ], $exception->getCode());
    }

    private function handleLaravelValidationException(LaravelValidationException $exception): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $exception->errors(),
            'timestamp' => now()->toISOString()
        ], 422);
    }

    private function handleHttpException(HttpException $exception): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $exception->getMessage() ?: 'HTTP Error',
            'status_code' => $exception->getStatusCode(),
            'timestamp' => now()->toISOString()
        ], $exception->getStatusCode());
    }

    private function handleGenericException(Throwable $exception): JsonResponse
    {
        $statusCode = method_exists($exception, 'getStatusCode') 
            ? $exception->getStatusCode() 
            : 500;

        $message = config('app.debug') 
            ? $exception->getMessage() 
            : 'Internal server error';

        return response()->json([
            'success' => false,
            'message' => $message,
            'error_code' => 'INTERNAL_ERROR',
            'timestamp' => now()->toISOString(),
            'debug' => config('app.debug') ? [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString()
            ] : null
        ], $statusCode);
    }

    private function logException(Throwable $exception, Request $request): void
    {
        $context = [
            'exception_class' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => auth()->id(),
            'request_data' => $request->except(['password', 'password_confirmation']),
            'headers' => $request->headers->all()
        ];

        if ($exception instanceof BusinessLogicException) {
            $context['error_code'] = $exception->getErrorCode();
            $context['business_context'] = $exception->getContext();
        }

        $this->loggingService->logError(
            'Exception occurred: ' . $exception->getMessage(),
            $context
        );
    }
}
