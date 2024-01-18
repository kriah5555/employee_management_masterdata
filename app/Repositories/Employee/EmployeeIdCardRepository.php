<?php

namespace App\Repositories\Employee;

use App\Models\Company\Employee\EmployeeIdCard;
use App\Interfaces\Employee\EmployeeIdCardInterface;

class EmployeeIdCardRepository implements EmployeeIdCardInterface
{
    public function getEmployeeIdCardByEmployeeProfileId($employee_profile_id)
    {
        return EmployeeIdCard::where(['employee_profile_id' => $employee_profile_id])->get();
    }

    public function deleteIdCard($employee_profile_id, $type)
    {
        return EmployeeIdCard::where(['type' => $type, 'employee_profile_id' => $employee_profile_id])->delete();
    }

    public function updateEmployeeIdCardsByEmployeeProfileId($details)
    {
        $conditions = [
            'employee_profile_id' => $details['employee_profile_id'],
            'type' => $details['type'],
        ];

        $values = [
            'file_id' => $details['file_id'],
        ];

        EmployeeIdCard::updateOrInsert($conditions, $values);
    }
}
