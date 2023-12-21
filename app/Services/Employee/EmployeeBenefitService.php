<?php

namespace App\Services\Employee;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Repositories\Employee\EmployeeBenefitsRepository;
use App\Repositories\Employee\EmployeeProfileRepository;

class EmployeeBenefitService
{

    public function __construct(
        protected EmployeeBenefitsRepository $employeeBenefitsRepository,
        protected EmployeeProfileRepository $employeeProfileRepository,
    ) {
    }

    public function getEmployeeBenefits($employee_profile_id)
    {
        
        try {

            $employee = $this->employeeProfileRepository->getEmployeeProfileById($employee_profile_id);
            $employee_benefits_details = [
                'social_secretary_number'   => $employee->employeeSocialSecretaryDetails->social_secretary_number,
                'contract_number'          => $employee->employeeSocialSecretaryDetails->contract_number,
                'benefits'                 => $employee->employeeBenefits,   
            ];
                
            return $employee_benefits_details;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }

        
    }

    public function createEmployeeBenefits($values, $employee_profile_id)
    {
        try {
            DB::connection('tenant')->beginTransaction();

                $employee = $this->employeeProfileRepository->getEmployeeProfileById($employee_profile_id);

                $employee->employeeBenefits()->create($values);

            DB::connection('tenant')->commit();
            return $employee;
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function updateEmployeeBenefits($values, $employee_profile_id)
    {

        try {
            DB::connection('tenant')->beginTransaction();

                $employee = $this->employeeProfileRepository->getEmployeeProfileById($employee_profile_id);

                $employee_benefits = $employee->employeeBenefits()->updateOrCreate(
                    ['id' => optional($employee->employeeBenefits)->id],
                    $values
                );

                $employee_benefits = $employee->employeeSocialSecretaryDetails()->updateOrCreate(
                    ['id' => optional($employee->employeeSocialSecretaryDetails)->id],
                    $values
                );

                // $employee->employeeSocialSecretaryDetails()->update($values);

                DB::connection('tenant')->commit();
                return $employee_benefits;
            return $employee_benefits;
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }
}
