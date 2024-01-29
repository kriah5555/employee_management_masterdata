<?php

namespace App\Services\Dimona;

use Exception;
use App\Models\Company\Company;
use Illuminate\Support\Facades\DB;
use App\Repositories\Dimona\DimonaTypeRepository;
use App\Repositories\EmployeeType\EmployeeTypeRepository;

class DimonaService
{

    public function __construct(
        protected DimonaTypeRepository $dimonaTypeRepository,
        protected EmployeeTypeRepository $employeeTypeRepository,
    ) {
    }

    public function getActiveDimonaTypes()
    {
        return $this->dimonaTypeRepository->getActiveDimonaTypes();
    }

    public function getDimaonStatusForCompany($company_id)
    {
        try {

            $employee_types = collect($this->employeeTypeRepository->getCompanyEmployeeTypes($company_id));
            $company_dimoan_emp_type_ids = $this->getAllEmployeeTypeIDsForCompany($company_id);
            return $employee_types->transform(function ($employee_type) use ($company_dimoan_emp_type_ids) {
                return [
                    'employee_type_id'   => $employee_type->id,
                    'employee_type_name' => $employee_type->name,
                    'status'             => in_array($employee_type->id, $company_dimoan_emp_type_ids),
                ];
            });

        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function updateEmpTypeDimoanConfigToCompany($company_id, $values)
    {
        try {
            DB::beginTransaction();

            $company = Company::findOrFail($company_id);
            $employee_type_ids = $values['employee_type_ids'] ?? [];

            // Sync the holiday codes to the company
            $company->dimoanEmployeeTypes()->sync($employee_type_ids);

            $company->refresh();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getAllEmployeeTypeIDsForCompany($company_id)
    {
        $company = Company::findOrFail($company_id);
        return $company->dimoanEmployeeTypes()->pluck('employee_types.id')->toArray();
    }

}
