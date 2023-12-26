<?php

namespace App\Interfaces\Employee;

use App\Models\Company\Employee\EmployeeProfile;

interface ResponsiblePersonInterface
{
    public function getCompanyResponsiblePersons($company_id);

    public function getResponsiblePersonById(string $responsible_person_id, string $company_id);

    public function deleteResponsiblePerson(string $responsible_person_id, string $company_id);

    public function createResponsiblePerson(array $responsible_person_details, string $company_id);

    public function updateResponsiblePerson(string $responsible_person_id, array $responsible_person_details, string $company_id);
}
