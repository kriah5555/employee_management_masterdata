<?php

namespace App\Http\Requests\Planning;

use App\Http\Requests\ApiRequest;

// use Illuminate\Foundation\Http\FormRequest;

class VacancyEmployeeRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'vacancy_id'          => 'required|integer',
            'request_status'      => 'required|integer|in:0,1,2,3,4', //1: Apply, 2: Save, 3: Ignore
            'vacancy_date'        => 'required|date_format:d-m-Y',
            'company_id'          => 'required|integer',
            'id'                  => 'nullable|exists:vacancy_post_employee,id',
            'employee_profile_id' => 'nullable|integer|exists:employee_profiles,user_id',
            'user_id'             => 'nullable|integer',
            'responded_by'        => 'nullable|integer',
        ];
    }

    // public function messages()
    // {
    //     return [
    //     ];
    // }
}
