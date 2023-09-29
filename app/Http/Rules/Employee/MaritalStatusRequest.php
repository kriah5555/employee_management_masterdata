<?php

namespace App\Http\Rules\Employee;

use App\Http\Rules\ApiRequest;

class MaritalStatusRequest extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $rules = [];
        if ($this->isMethod('post') || $this->isMethod('put')) {
            $rules = [
                'sort_order' => 'required|integer',
                'name'       => 'required|string|max:255'
            ];
            // if ($this->isMethod('post')) {
            //     $rules = array_merge($rules, [
            //         'name' => 'required|string|max:255',
            //     ]);
            // } else {
            //     $maritalStatus = $this->route('marital_status');
            //     $rules = array_merge($rules, [
            //         'name' => 'required|string|max:255|unique:marital_statuses,name,' . $maritalStatus->id,
            //     ]);
            // }
        }
        return $rules;

    }
    public function messages()
    {
        return [];
    }
}