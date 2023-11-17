<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Company\CostCenter;
use App\Models\Company\Location;

class UniqueCostCenterNumberInCompanyRule implements ValidationRule
{
    protected $cost_center;

    public function __construct($cost_center = null)
    {
        $this->cost_center = $cost_center;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $cost_center_id = $this->cost_center;
        $query = CostCenter::where('cost_center_number', $value)
            ->when(isset($cost_center_id), function ($query) use ($cost_center_id) {
                $query->where('id', '!=', $cost_center_id);
            });

        if ($query->exists()) {
            $fail("The $attribute is not unique within the company.");
        }
    }
}
