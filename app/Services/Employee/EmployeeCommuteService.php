<?php

namespace App\Services\Employee;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Repositories\Employee\EmployeeCommuteRepository;
use App\Repositories\Employee\EmployeeProfileRepository;

class EmployeeCommuteService
{

    public function __construct(
        protected EmployeeCommuteRepository $employeeCommuteRepository,
        protected EmployeeProfileRepository $employeeProfileRepository,
    ) {
    }

    public function getEmployeeCommuteDetails($employee_profile_id)
    {
        try {

            $employee = $this->employeeProfileRepository->getEmployeeProfileById($employee_profile_id);
            // dd($employee->employeeCommute->toArray());

            $employee_benefits_details = [
                'employee_commute_details'   => $employee->employeeCommute->load('commuteType', 'location'),
            ];
                
            return $employee_benefits_details;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        } 
    }

    public function createEmployeeCommuteDetails($values, $employee_profile_id)
    {
        try {
            DB::connection('tenant')->beginTransaction();

                $employee = $this->employeeProfileRepository->getEmployeeProfileById($employee_profile_id);

                $employee->employeeCommute()->createMany($values['employee_commute_details']);

            DB::connection('tenant')->commit();
            return $employee;
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function updateEmployeeCommuteDetails($values, $employee_profile_id)
    {

        try {
            DB::connection('tenant')->beginTransaction();

                $employee = $this->employeeProfileRepository->getEmployeeProfileById($employee_profile_id);

                $employee->employeeCommute()->delete();

                $employee->employeeCommute()->createMany($values['employee_commute_details']);

            DB::connection('tenant')->commit();
            return $employee;
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }
}
