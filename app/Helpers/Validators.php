<?php

if (!function_exists('validateStringByPattern')) {
    function validateStringByPattern($string, $pattern)
    {
        if (preg_match($pattern, $string)) {
            return true;
        }
        return false;
    }
}
if (!function_exists('validateCompanyRszNumber')) {
    function validateCompanyRszNumber($string)
    {
        $pattern = '/^BE\d{4}\.\d{3}\.\d{3}$/i';
        return validateStringByPattern($string, $pattern);
    }
}

if (!function_exists('validateEmail')) {
    function validateEmail($string)
    {
        // $pattern = '/^$([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})/';
        $pattern = '/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        return validateStringByPattern($string, $pattern);
    }
}


if (!function_exists('validateRole')) {
    function validateRole($string)
    {
        // $pattern = '/^$([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})/';
        $pattern = '/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        return validateStringByPattern($string, $pattern);
    }
}
