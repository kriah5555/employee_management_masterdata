<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function callback(Request $request)
    {
        $response = Http::post(config('services.auth_server.base_uri') . '/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => config('services.auth_server.client_id'),
            'client_secret' => config('services.auth_server.client_secret'),
            'redirect_uri' => config('services.auth_server.redirect'),
            'code' => $request->code,
        ]);
        
        $data = $response->json();
        
        if ($response->ok()) {
            Auth::loginUsingId($data['user_id']);
            return redirect('/home');
        } else {
            return redirect('/login')->with('error', $data['message']);
        }
    }
}
