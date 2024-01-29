<?php

namespace App\Http\Requests\Dimona;

use App\Http\Requests\ApiRequest;

class DimonaErrorCodeRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'description' => 'required|string',
        ];
    }
}
