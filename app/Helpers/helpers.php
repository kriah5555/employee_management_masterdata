<?php

if (!function_exists('api_response')) {
    function api_response($status, $message, $data = '', $server_error_status = '')
    {
        $return_data = [
            'status'  => $status,
            'message' => $message,
        ];

        if ($data) {
            $return_data['data'] = $data;
        }

        return $server_error_status ? response()->json($return_data, $server_error_status) : response()->json($return_data);
    }
}