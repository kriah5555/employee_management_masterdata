<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Sector\Sector;
use App\Services\Sector\SectorService;

class MinimumSalariesCategoriesRule implements ValidationRule
{
    protected $sectorService;

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $sector_id = request()->route('id');
        print_r($sector_id);
        exit;
        $sector = Sector::findOrFail($sector_id);
        $categories = $sector->salaryConfig->category;
        if (count($value) != $categories) {
            $fail('Salaries do not match number of categories.');
        }
    }
}