<?php

namespace App\Services\Dimona;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class RequestDimona
{

    public function sendDimonaRequest($dimonaObject, $url)
    {
        // try {
        $data = is_array($dimonaObject) ? json_encode($dimonaObject) : $dimonaObject;
        $client = new Client();
        $headers = [
            'Content-Type' => 'application/json',
        ];
        $requestUrl = env('DIMONA_SERVICE_URL') . $url;
        $request = new Request('POST', $requestUrl, $headers, $data);
        $res = $client->send($request);
        $body = json_decode($res->getBody()->getContents());
        return $body->status;
        // } catch (\Exception $e) {
        //     dd($res);
        //     return ['status' => false];
        // }
    }
}
