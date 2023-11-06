<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class ExistsInMasterDatabaseRule implements ValidationRule
{
    public function __construct(protected $table_name, protected $column = 'id')
    {
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!DB::connection('master')->table($this->table_name)->where($this->column, $value)->exists()) {
            $fail("The selected value for {$attribute} does not exist.");
        }
    }
}
