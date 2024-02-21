<?php

namespace App\Services\Configuration;

use App\Models\Configuration\Configuration;
use Illuminate\Http\JsonResponse;

class FlexSalaryService
{
    public function getFlexSalaryByKey($key)
    {
        $data = Configuration::where('key', $key)->get()->first();


        return $data ? (
            [
                'europian_format' => formatToEuropeCurrency($data->value),
                'number_format' => $data->value
            ]) : [
            'europian_format' => "",
            'number_format' => ""
        ];
    }

    public function createOrUpdateFlexSalary($data)
    {

        return Configuration::updateOrCreate(
            ['key' => 'flex_min_salary'],
            ['value' => formatToNumber($data->value)]
        );
    }
}
