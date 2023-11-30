<?php

namespace App\Http\Requests;

use App\Http\Requests\ApiRequest;

class MealVoucherRequest extends ApiRequest
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
                'amount'     => 'required|string|regex:/^\d{1,3}(?:\.\d{3})*(?:,\d+)?$/',
            ];
        }
        return $rules;

    }
    public function messages()
    {
        return [];
    }
}