<?php

namespace App\Services\Dimona;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class RequestDimona
{

    public function sendDimonaRequest($dimonaObject)
    {
        $data = is_array($dimonaObject) ? json_encode($dimonaObject) : $dimonaObject;
        $client = new Client();
        $headers = [
            'Content-Type' => 'application/json',
        ];
        $url = env('DIMONA_SERVICE_URL') . "/api/send-dimona";
        dd($data);
        $request = new Request('POST', $url, $headers, $data);
        return $client->send($request);
    }
}
