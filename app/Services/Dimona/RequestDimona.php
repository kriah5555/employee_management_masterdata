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
        $data = is_array($dimonaObject) ? json_encode($dimonaObject): $dimonaObject;

        $client = new Client();

        $headers = [
            'Content-Type' => 'application/json',
        ];
        $request = new Request('POST', env('DIMONA_SERVICE_URL'), $headers, $data);
        $res = $client->send($request);

        return $res;
    }
}
