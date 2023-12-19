<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Services\Contract\ContractService;

class HttpRequestService
{
    public function sendRequest($base_url, $body, $method) 
    {
        $response = Http::send($method, $base_url, [
            'json' => $body,
        ]);

        return $response->json();
    }
}
