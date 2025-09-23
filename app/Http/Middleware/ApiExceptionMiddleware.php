<?php

namespace App\Http\Middleware;

use App\Exceptions\BusinessLogicException;
use App\Exceptions\ValidationException;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\UnauthorizedAccessException;
use App\Services\LoggingService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException as LaravelValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class ApiExceptionMiddleware
{
    public function __construct(
        private LoggingService $loggingService
    ) {}

    public function handle(Request $request, Closure $next)
    {
        try {
            return $next($request);
        } catch (Throwable $exception) {
            return $this->handleException($exception, $request);
        }
    }

    private function handleException(Throwable $exception, Request $request)
    {
        // Логируем исключение
        $this->logException($exception, $request);

        // Определяем тип ответа
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->handleApiException($exception);
        }

        // Для веб-запросов пробрасываем исключение дальше
        throw $exception;
    }

    private function handleApiException(Throwable $exception)
    {
        if ($exception instanceof BusinessLogicException) {
            return response()->json([
                'success' => false,
                'error_code' => $exception->getErrorCode(),
                'message' => $exception->getMessage(),
                'context' => $exception->getContext(),
                'timestamp' => now()->toISOString()
            ], $exception->getCode());
        }

        if ($exception instanceof ValidationException) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'errors' => $exception->getErrors(),
                'data' => $exception->getData(),
                'timestamp' => now()->toISOString()
            ], $exception->getCode());
        }

        if ($exception instanceof ResourceNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'resource_type' => $exception->getResourceType(),
                'resource_id' => $exception->getResourceId(),
                'timestamp' => now()->toISOString()
            ], $exception->getCode());
        }

        if ($exception instanceof UnauthorizedAccessException) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'action' => $exception->getAction(),
                'resource' => $exception->getResource(),
                'context' => $exception->getContext(),
                'timestamp' => now()->toISOString()
            ], $exception->getCode());
        }

        if ($exception instanceof LaravelValidationException) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $exception->errors(),
                'timestamp' => now()->toISOString()
            ], 422);
        }

        if ($exception instanceof HttpException) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage() ?: 'HTTP Error',
                'status_code' => $exception->getStatusCode(),
                'timestamp' => now()->toISOString()
            ], $exception->getStatusCode());
        }

        // Общие исключения
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
