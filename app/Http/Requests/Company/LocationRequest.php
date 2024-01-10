<?php

namespace App\Http\Requests\Company;

use App\Services\Company\LocationService;
use App\Http\Requests\ApiRequest;
use App\Rules\ResponsiblePersonExistsRule;

class LocationRequest extends ApiRequest
{
    public function rules(): array
    {
        $location_rules = LocationService::getLocationRules(false);
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            unset($location_rules['company']);
        }
        $location_rules['responsible_persons'] = [
            'nullable',
            'array'
        ];
        $location_rules['responsible_persons.*'] = [
            new ResponsiblePersonExistsRule(getCompanyId())
        ];
        return $location_rules;
    }

    public function messages()
    {
        return [
            'status.boolean'   => 'The address status field must be a boolean value.',
            'address.required' => 'The address field is required.',
        ];
    }
}
