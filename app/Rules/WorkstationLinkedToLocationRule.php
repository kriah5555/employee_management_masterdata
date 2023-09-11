<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Rule;
use App\Models\Location;


class WorkstationLinkedToLocationRule implements Rule
{
    protected $location_id;

    public function __construct($location_id)
    {
        $this->location_id = $location_id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Specify table aliases to avoid ambiguity
        return Location::where('locations.id', $this->location_id)
            ->whereHas('workstations', function ($query) use ($value) {
                $query->where('workstations.id', $value);
            })
            ->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The selected :attribute is not linked to the specified location.';
    }
}
