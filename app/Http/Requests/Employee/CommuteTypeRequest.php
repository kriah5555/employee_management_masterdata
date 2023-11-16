<?php

namespace App\Http\Requests\Employee;

use App\Http\Requests\ApiRequest;

class CommuteTypeRequest extends ApiRequest
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
                'name'       => 'required|string|max:255',
            ];
        }
        return $rules;

    }
    public function messages()
    {
        return [];
    }
}