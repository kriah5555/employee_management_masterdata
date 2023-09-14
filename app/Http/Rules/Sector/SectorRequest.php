<?php

namespace App\Http\Rules\Sector;

use App\Http\Rules\ApiRequest;
use Illuminate\Validation\Rule;
use App\Rules\SectorExperienceRule;
use App\Rules\SectorAgeSalaryRule;

class SectorRequest extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name'               => 'required|string|max:255',
            'paritair_committee' => 'required|string|max:255',
            'category'           => 'required|integer|max:50',
            'description'        => 'nullable|string',
            'status'             => 'required|boolean',
            'employee_types'     => 'nullable|array',
            'employee_types.*'   => [
                Rule::exists('employee_types', 'id'),
            ],
            'experience'         => ['required', 'array', new SectorExperienceRule],
            'age'                => ['required', 'array', new SectorAgeSalaryRule]
        ];
    }
    public function messages()
    {
        return [
            'name.required'                     => 'Name is required.',
            'name.string'                       => 'Name must be a string.',
            'name.max'                          => 'Name cannot be greater than 255 characters.',
            'paritair_committee.required'       => 'Paritair committee is required.',
            'paritair_committee.string'         => 'Paritair committee must be a string.',
            'paritair_committee.max'            => 'Paritair committee cannot be greater than 255 characters.',
            'description.string'                => 'Description must be a string.',
            'description.max'                   => 'Description cannot be greater than 255 characters.',
            'status.boolean'                    => 'Status must be a boolean value.',
            'category.required'                 => 'Category is required.',
            'category.max'                      => 'Category cannot be greater than 50.',
            'experience.sector_experience_rule' => 'test'
        ];
    }
}