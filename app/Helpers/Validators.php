<?php
use Spatie\TranslationLoader\LanguageLine;
use App\Models\User\User;
use Illuminate\Support\Facades\Http;
use App\Models\Tenant;
use Illuminate\Support\Facades\Request;

if (!function_exists('validateCompanyRszNumber')) {
    function validateCompanyRszNumber($string)
    {
        $pattern = '/^BE\d{4}\.\d{3}\.\d{3}$/i';
        if (preg_match($pattern, $string)) {
            return true;
        }
        return false;
    }
}

if (!function_exists('validateEmail')) {
    function validateEmail($string)
    {
        $pattern = '/^BE\d{4}\.\d{3}\.\d{3}$/i';
        if (preg_match($pattern, $string)) {
            return true;
        }
        return false;
    }
}
