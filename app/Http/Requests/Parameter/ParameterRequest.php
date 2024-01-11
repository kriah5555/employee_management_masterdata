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
        return $parameters;

    }
    public function messages()
    {
        return [
            'value.required' => 'Value is required.',
        ];
    }
}
