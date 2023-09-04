<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Closure;

class ServiceRegistryMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('env') == 'production') {
            $serviceRegistryUrl = env('SERVICE_REGISTRY'); // Replace with your service registry URL

            // Check if the request comes from the service registry
            if ($request->header('Origin') !== $serviceRegistryUrl) {
                return response('Unauthorized', 401);
            }
        }
        return $next($request);
    }
}