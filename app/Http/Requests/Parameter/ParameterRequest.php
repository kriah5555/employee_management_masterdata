<?php

namespace App\Http\Requests\Parameter;

use App\Http\Requests\ApiRequest;
use Illuminate\Validation\Rule;

class ParameterRequest extends ApiRequest
{
    protected $sectorService;

    /**
     * Get the validation parameters that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $parameters = [];
        if ($this->route()->getName() == 'get-default-parameters') {
            $parameters['type'] = 'required|integer|in:1,2,3,4,5';
        }
        if ($this->route()->getName() == 'update-default-parameter') {
            // $parameters['description'] = 'required|string';
            $parameters['value'] = 'required';
        }
        if ($this->route()->getName() == 'get-parameters') {
            $parameters['type'] = 'required|integer|in:1,2,3';
            $type = $this->input('type');
            if ($type == 1) {
                $parameters['id'] = [
                    'required',
                    'integer',
                    Rule::exists('employee_types', 'id'),
                ];
            }
            if ($type == 2) {
                $parameters['id'] = [
                    'required',
                    'integer',
                    Rule::exists('sectors', 'id'),
                ];
            }
            if ($type == 3) {
                $parameters['id'] = [
                    'required',
                    'integer',
                    Rule::exists('employee_types', 'id'),
                ];
                $parameters['sector_id'] = [
                    'required',
                    'integer',
                    Rule::exists('sectors', 'id'),
                ];
            }
        }
        if ($this->route()->getName() == 'update-parameter') {
            $parameters['type'] = 'required|integer|in:1,2,3';
            $parameters['value'] = [
                'required'
            ];
        }
        if ($this->route()->getName() == 'get-company-parameters') {
            $parameters['type'] = 'required|integer|in:1,2,3,4,5';
            $type = $this->input('type');
            if ($type == 1) {
                $parameters['employee_type_id'] = [
                    'required',
                    'integer',
                    Rule::exists('master.employee_types', 'id'),
                ];
            }
            if ($type == 2) {
                $parameters['sector_id'] = [
                    'required',
                    'integer',
                    Rule::exists('master.sectors', 'id'),
                ];
            }
            if ($type == 3) {
                $parameters['employee_type_id'] = [
                    'required',
                    'integer',
                    Rule::exists('master.employee_types', 'id'),
                ];
                $parameters['sector_id'] = [
                    'required',
                    'integer',
                    Rule::exists('master.sectors', 'id'),
                ];
            }
            if ($type == 5) {
                $parameters['location_id'] = [
                    'required',
                    'integer',
                    Rule::exists('locations', 'id'),
                ];
            }
        }
        if ($this->route()->getName() == 'update-company-parameters') {
            $parameters['parameter_name'] = [
                'required',
                'string',
                Rule::exists('master.parameters', 'name'),
            ];
            $parameterName = $this->route('parameter_name');
            if (in_array($parameterName, ['PAR_1A', 'PAR_1B'])) {
                $parameters['employee_type_id'] = [
                    'required',
                    'integer',
                    Rule::exists('master.employee_types', 'id'),
                ];
            }
            if (in_array($parameterName, ['PAR_2A', 'PAR_2B', 'PAR_2C', 'PAR_2D', 'PAR_2E', 'PAR_2F', 'PAR_2G', 'PAR_2H', 'PAR_2I', 'PAR_2J'])) {
                $parameters['sector_id'] = [
                    'required',
                    'integer',
                    Rule::exists('master.sectors', 'id'),
                ];
            }
            if (in_array($parameterName, ['PAR_3A', 'PAR_3B', 'PAR_3C'])) {
                $parameters['employee_type_id'] = [
                    'required',
                    'integer',
                    Rule::exists('master.employee_types', 'id'),
                ];
                $parameters['sector_id'] = [
                    'required',
                    'integer',
                    Rule::exists('master.sectors', 'id'),
                ];
            }
            if (in_array($parameterName, ['PAR_5A', 'PAR_5B'])) {
                $parameters['location_id'] = [
                    'required',
                    'integer',
                    Rule::exists('locations', 'id'),
                ];
            }
            $parameters['use_default'] = [
                'required',
                'boolean',
            ];
            $parameters['value'] = [
                'required_if:use_default,false'
            ];
        }
        return $parameters;

    }
    protected function prepareForValidation()
    {
        if ($this->route()->getName() == 'update-company-parameters') {
            $this->merge(['parameter_name' => $this->route('parameter_name')]);
            $this->formatParameters();
        }
    }
    protected function formatParameters()
    {
        $type = $this->input('type');
        if ($type == 1 || $type == 3) {
            $newParams['employee_type_id'] = $this->input('id');
        } elseif ($type == 2) {
            $newParams['sector_id'] = $this->input('id');
        } elseif ($type == 5) {
            $newParams['location_id'] = $this->input('id');
        }
        $this->merge($newParams);
    }
    public function messages()
    {
        return [
            'value.required' => 'Value is required.',
        ];
    }
}
