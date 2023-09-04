<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class SetCurrentUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // $uid = $request->route('uid');

        // // Fetch the user by UID
        // $user = User::where('uid', $uid)->first();

        // if (!$user) {
        //     return response()->json(['error' => 'User not found'], 404);
        // }

        // // Set the user as the current active user
        // auth()->login($user);

        return $next($request);
    }
}