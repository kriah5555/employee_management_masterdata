<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthService
{
    public function setActiveUserByUid($uid)
    {
        $user = User::findOrFail($uid);
        if ($user) {
            // Log in the user
            Auth::login($user);
        }
    }
}