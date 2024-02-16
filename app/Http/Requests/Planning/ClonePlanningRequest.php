<?php

namespace App\Http\Requests\Planning;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\ApiRequest;

class ClonePlanningRequest extends ApiRequest
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
            'from_year' => ['required'],
            'from_week' => ['required'],
            'to_year' => ['required'],
            'to_week' => ['required'],
            'employee_names' => ['required'],
            // 'employee_types' => ['required'],
            'location_id' => ['required']
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $toYear = $this->input('to_year');
            $fromYear = $this->input('from_year');
            $toWeek = $this->input('to_week');
            $fromWeek = $this->input('from_week');
            if ((count($fromWeek) > count($toWeek)) &&  count($toWeek) !== 0) {
                $this->validator->errors()->add('message', 'from weeks should be one or match the count of to_weeks');
            } else if ($this->validWeeks($fromYear, $fromWeek, $toYear, $toWeek)) {
                return;
            } else {
                if (count($toWeek) !== 0) {
                    $this->validator->errors()->add('message', 'to week should be a week after the from week ');
                }
            }
        });
    }

    public function validWeeks($fromYear, $fromWeek, $toYear, $toWeek)
    {
        $isValid = true;

        if ($toYear > $fromYear) {
            return $isValid;
        } elseif ($toYear == $fromYear) {
            // Compare the week arrays if years are equal
            foreach ($fromWeek as $fromValue) {
                foreach ($toWeek as $toValue) {
                    // Compare each element in $fromWeek to all elements in $toWeek
                    if ($toValue <= $fromValue) {
                        $isValid = false;
                        break; // Stop all comparisons if an invalid week is found
                    }
                }
            }
            return $isValid;
        }
    }
}
