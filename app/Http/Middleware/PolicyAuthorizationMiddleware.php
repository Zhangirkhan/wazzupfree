<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PolicyAuthorizationMiddleware
{
    public function handle(Request $request, Closure $next, string $policy, string $method = 'view'): Response
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
        
        // Получаем модель из параметров маршрута
        $model = $this->getModelFromRoute($request, $policy);
        
        if ($model && !$user->can($method, $model)) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden'
            ], 403);
        }
        
        return $next($request);
    }
    
    private function getModelFromRoute(Request $request, string $policy): ?object
    {
        $routeParams = $request->route()->parameters();
        
        // Пытаемся найти модель в параметрах маршрута
        foreach ($routeParams as $param) {
            if (is_object($param) && method_exists($param, 'getKey')) {
                return $param;
            }
        }
        
        return null;
    }
}
