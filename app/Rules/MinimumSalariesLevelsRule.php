<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Sector\Sector;
use App\Services\Sector\SectorService;

class MinimumSalariesLevelsRule implements ValidationRule
{
    protected $sectorService;

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $sector_id = request()->route('sector_id');
        $sector = Sector::findOrFail($sector_id);
        $steps = $sector->salaryConfig->steps;
        if (count($value) != $steps) {
            $fail('Salaries do not match number of experience level.');
        }
    }
}