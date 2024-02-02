<?php

namespace App\Http\Requests\Planning;

use App\Http\Requests\ApiRequest;
use App\Models\Company\DashboardAccess;
use Illuminate\Validation\Rule;


class UurroosterRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'date'        => 'required|date_format:d-m-Y',
            'location_id' => [
                'bail',
                'integer',
                Rule::exists('tenant.locations', 'id'),
            ]
        ];
        if ($this->route()->getName() == 'open-uurrooster') {
            $rules['access_key'] = 'required|string';
        }
        return $rules;
    }
    // protected function passedValidation()
    // {
    //     if ($this->route()->getName() == 'open-uurrooster') {
    //         $this->validateAccesskey();
    //     } else {
    //         $this->merge(['access_type' => 'company']);
    //     }
    // }
    protected function prepareForValidation()
    {
        if ($this->route()->getName() == 'open-uurrooster') {
            $this->validateAccesskey();
        } else {
            $this->merge([
                'access_type'        => 'company',
                'location_selection' => true
            ]);
        }
    }

    protected function validateAccesskey()
    {
        $input = $this->input();
        $access_key = $input['access_key'];
        $dashboardAccess = DashboardAccess::where('access_key', $access_key)->first();
        if ($dashboardAccess) {
            setTenantDBByCompanyId($dashboardAccess->company_id);
            $this->merge(['access_type' => $dashboardAccess->type]);
            if ($dashboardAccess->type == 'location') {
                $this->merge([
                    'location_id'        => $dashboardAccess->location_id,
                    'location_selection' => false
                ]);
            } else {
                $this->merge(['location_selection' => true]);
            }
        } else {
            throw new \Illuminate\Auth\Access\AuthorizationException('This action is unauthorized.');
        }
    }
}
