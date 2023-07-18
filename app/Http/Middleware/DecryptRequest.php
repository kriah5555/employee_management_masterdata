<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\EncryptDecryptController;

class DecryptRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if ($request !== null) {
            $input = $request->all();
            //for decrypting request data, converting to array and replacing in request
            $decrypteddata = json_decode(EncryptDecryptController::decryptcode(json_encode($input)));
            $decryptedarr = json_decode(json_encode($decrypteddata), true);
            $request->replace($decryptedarr);
        } else {
            $message = json_encode(['status' => 400, 'message' => 'Please provide valid data.']);
            return response($message);
        }
        return $next($request);
    }
}
