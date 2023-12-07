<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Repositories\Employee\ResponsiblePersonRepository;

class ResponsiblePersonExistsRule implements ValidationRule
{
    public function __construct(protected $company_id)
    {

    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $responsible_person = app(ResponsiblePersonRepository::class)->getResponsiblePersonById($value, $this->company_id);
        if (empty($responsible_person)) {
            $fail(':attribute is invalid');
        }
    }
}
