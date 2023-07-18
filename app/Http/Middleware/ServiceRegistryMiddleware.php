<?php

namespace App\Http\Middleware;

use Closure;

class ServiceRegistryMiddleware
{
    public function handle($request, Closure $next)
    {
        $serviceRegistryUrl = env('SERVICE_REGISTRY'); // Replace with your service registry URL

        // Check if the request comes from the service registry
        if ($request->header('Origin') !== $serviceRegistryUrl) {
            return response('Unauthorized', 401);
        }

        return $next($request);
    }
}

