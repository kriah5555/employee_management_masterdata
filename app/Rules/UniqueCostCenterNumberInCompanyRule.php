<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Company\CostCenter;
use App\Models\Company\Location;

class UniqueCostCenterNumberInCompanyRule implements ValidationRule
{
    protected $company_id;
    protected $cost_center;

    public function __construct($company_id, $cost_center = null)
    {
        $this->company_id = $company_id;
        $this->cost_center = $cost_center;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $company_id = $this->company_id;
        $cost_center = $this->cost_center;

        $query = CostCenter::where('cost_center_number', $value)
            ->whereHas('location', function ($query) use ($company_id) {
                $query->where('company', $company_id);
            })
            ->when(isset($cost_center), function ($query) use ($cost_center) {
                $query->where('id', '!=', $cost_center->id);
            });

        if ($query->exists()) {
            $fail("The $attribute is not unique within the company.");
        }
    }
}
