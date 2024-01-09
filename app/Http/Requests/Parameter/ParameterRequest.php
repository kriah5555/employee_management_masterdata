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
        if ($this->route()->getName() == 'get-employee-type-parameters') {
            // $parameters['description'] = 'required|string';
            $parameters['employee_type_id'] = [
                'required',
                'integer',
                Rule::exists('employee_types', 'id'),
            ];
        }
        if ($this->route()->getName() == 'update-employee-type-parameter') {
            // $parameters['employee_type_id'] = [
            //     'required',
            //     'integer',
            //     Rule::exists('employee_types', 'id'),
            // ];
            // $parameters['name'] = [
            //     'required',
            //     'string',
            //     Rule::exists('parameters', 'name'),
            // ];
            // $parameters['use_default'] = [
            //     'required',
            //     'boolean',
            // ];
            // $parameters['value'] = [
            //     'nullable',
            //     !$this->request->get('use_default') ? 'required' : '',
            // ];
            $parameters['value'] = [
                'required'
            ];
        }
        if ($this->route()->getName() == 'get-sector-parameters') {
            // $parameters['description'] = 'required|string';
            $parameters['sector_id'] = [
                'required',
                'integer',
                Rule::exists('sectors', 'id'),
            ];
        }
        return $parameters;

    }
    public function messages()
    {
        return [
            'value.required' => 'Value is required.',
        ];
    }
}
