<?php

namespace App\Http\Middleware;

use App\Services\LoggingService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class ApiLoggingMiddleware
{
    public function __construct(
        private LoggingService $loggingService
    ) {}

    public function handle(Request $request, Closure $next): BaseResponse
    {
        $startTime = microtime(true);
        
        $response = $next($request);
        
        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000; // в миллисекундах
        
        // Логируем только API запросы
        if ($request->is('api/*')) {
            $this->loggingService->logApiRequest($request, $response);
        }
        
        return $response;
    }
}
