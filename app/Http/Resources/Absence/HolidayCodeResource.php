<?php

namespace App\Http\Resources\Absence;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HolidayCodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                                => $this->id,
            'holiday_code_name'                 => $this->holiday_code_name,
            'count'                             => $this->count,
            'internal_code'                     => $this->internal_code,
            'description'                       => $this->description,
            'holiday_type'                      => $this->formatHolidayType($this->holiday_type),
            'count_type'                        => $this->formatCountType($this->count_type),
            'icon_type'                         => $this->formatIconType($this->icon_type),
            'consider_plan_hours_in_week_hours' => $this->consider_plan_hours_in_week_hours,
            'employee_category'                 => $this->formatEmployeeCategory($this->employee_category),
            'contract_type'                     => $this->formatContractType($this->contract_type),
            'status'                            => $this->status,
            'type'                              => $this->formatType($this->type),
        ];
    }

    public function formatEmployeeCategory($categories)
    {
        $employeeCategories = config('absence.HOLIDAY_EMPLOYEE_CATEGORY_OPTIONS');
        return array_map(function ($category) use ($employeeCategories) {
            return [
                'value' => $category,
                'label' => $employeeCategories[$category],
            ];
        }, json_decode($categories));
    }

    public function formatHolidayType($holidayType)
    {
        $holidayTypes = config('absence.HOLIDAY_TYPE_OPTIONS');
        return [
            'value' => $holidayType,
            'label' => $holidayTypes[$holidayType],
        ];
    }

    public function formatCountType($countType)
    {
        $countTypes = config('absence.HOLIDAY_COUNT_TYPE_OPTIONS');
        return [
            'value' => $countType,
            'label' => $countTypes[$countType],
        ];
    }

    public function formatIconType($iconType)
    {
        $iconTypes = config('absence.HOLIDAY_ICON_TYPE_OPTIONS');
        return [
            'value' => $iconType,
            'label' => $iconTypes[$iconType],
        ];
    }

    public function formatType($type)
    {
        $types = config('absence.HOLIDAY_OPTIONS');
        return [
            'value' => $type,
            'label' => $types[$type],
        ];
    }

    public function formatContractType($contractType)
    {
        $contractTypes = config('absence.HOLIDAY_CONTRACT_TYPE_OPTIONS');
        return [
            'value' => $contractType,
            'label' => $contractTypes[$contractType],
        ];
    }
}
