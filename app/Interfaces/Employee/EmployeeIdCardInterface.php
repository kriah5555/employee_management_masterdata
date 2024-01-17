<?php

namespace App\Interfaces\Employee;

use App\Models\Company\Employee\EmployeeProfile;

interface EmployeeIdCardInterface
{
    public function getEmployeeIdCardByEmployeeProfileId($employee_profile_id);

    // public function deleteEmployeeIdCard($employee_profile_id);

//     public function createEmployeeIdCards(array $responsible_person_details, string $company_id);

//     public function updateEmployeeIdCards(string $responsible_person_id, array $responsible_person_details, string $company_id);
}
