<?php

namespace App\Http\Requests\Planning;

use App\Http\Requests\ApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class VacancyUpdateRequest extends ApiRequest
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
            'id'             => 'nullable',
            'name'           => 'nullable|string',
            'location'       => 'required|integer|exists:locations,id',
            'workstations'   => 'required|integer|exists:workstations,id',
            'functions'      => 'required|integer',
            'employee_types' => 'required|array',
            'start_date'     => 'required|date_format:d-m-Y',
            'start_time'     => 'required|date_format:H:i',
            'end_time'       => 'required|date_format:H:i',
            'vacancy_count'  => 'required_if:status,1|integer|min:0',
            'approval_type'  => 'required|integer|in:0,1', // 0: Manual, 1: Auto
            'extra_info'     => 'nullable|string',
            'status'         => 'required|integer|in:0,1,2', // 1: Open, 0: deleted, 2: drafted
            'repeat_type'    => 'required|integer|int:0,1,2,3', //0: one time, 1: daily, 2: weekly, 4: monthly
            'end_date'       => 'nullable|date|after_or_equal:today',
            ''
        ];
    }

    public function messages()
    {
        return [
            'location_id.required' => t('Location id is required.'),
            'location_id.exists'   => t('Loction not found'),
            // ''
            // 'name.max'           => 'Employee type cannot be greater than 255 characters.',
            // 'description.string' => 'Description must be a string.',
            // 'description.max'    => 'Description cannot be greater than 255 characters.',
            // 'status.boolean'     => 'Status must be a boolean value.',
            // 'contract_types.*'   => 'Invalid contract type'
        ];
    }
}
