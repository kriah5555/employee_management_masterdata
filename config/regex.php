<?php

return [
    // common regex
    
    'TIME_FORMAT_REGEX'            => '/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/',

    'EUROPE_CURRENCY_FORMAT_REGEX' => '/^\d{1,3}(?:\.\d{3})*(?:,\d+)?$/',

    'PHONE_REGEX'                  => '/^(\+[0-9]{1,4}\s[0-9]{1,3}\s[0-9]{1,3}\s[0-9\s]+)$/',

    'EMAIL_REGEX'                  => '/^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$/',
];
