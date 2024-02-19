<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $language = $request->header('language') ? $request->header('language') : config('constants.DEFAULT_LANGUAGE');
        if (in_array($language, config('app.available_locales'))) {
            app()->setLocale($language);
        }
        
        return $next($request);
    }
}
