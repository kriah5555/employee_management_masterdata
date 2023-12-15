<?php

namespace App\Rules;

use Closure;
use App\Models\MealVoucher;
use Illuminate\Contracts\Validation\ValidationRule;

class MealVoucherRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    { 
        $meal_voucher = MealVoucher::find($value);

        if (!$meal_voucher) {
            $fail('Invalid :attribute');
        }   
    }
}
