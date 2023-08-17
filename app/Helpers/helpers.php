<?php

if (!function_exists('api_response')) {
    function api_response($status, $message, $data = '', $server_error_status = '')
    {
        $return_data = [
            'success'  => $status,
            'message' => $message,
        ];

        if ($data) {
            $return_data['data'] = $data;
        }

        return $server_error_status ? response()->json($return_data, $server_error_status) : response()->json($return_data);
    }
}

if (!function_exists('hasDuplicates')) {
    function hasDuplicates(array $array): bool {
        $seen = [];
        foreach ($array as $number) {
            if (isset($seen[$number])) {
                return true; // Duplicate found
            }
            $seen[$number] = true;
        }
        return false; // No duplicates found
    }
}