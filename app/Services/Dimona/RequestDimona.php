<?php

namespace App\Services\Dimona;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class RequestDimona
{
    // const DIMONA_URL=''
    public function __construct()
    {
    }

    public function sendDimonaRequest($dimonaObject)
    {
        $client = new Client();

        $headers = [
            'Content-Type' => 'application/json',
        ];

        $request = new Request('POST', env('DIMONA_SERVICE_URL'), $headers, $dimonaObject);
        $res = $client->send($request);

        return $res;
    }
}
