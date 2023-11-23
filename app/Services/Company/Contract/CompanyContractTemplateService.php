<?php

namespace App\Services\Company\Contract;

use Illuminate\Support\Facades\DB;
use App\Models\Company\Contract\CompanyContractTemplate;
use App\Services\BaseService;
use Exception;
use App\Services\SocialSecretary\SocialSecretaryService;
use App\Services\Sector\SectorService;
use App\Services\CompanyService;
use App\Services\EmployeeType\EmployeeTypeService;

class CompanyContractTemplateService extends BaseService
{
    protected $employee_type_service;

    public function __construct()
    {
        parent::__construct(CompanyContractTemplate::class);
        $this->employee_type_service = app(EmployeeTypeService::class);
    }

    public function getOptionsToCreate()
    {
        $company_id = request()->header('Company-Id');
        try {
            return [
                'employee_types'     => $this->employee_type_service->getCompanyEmployeeTypes($company_id),
                'tokens'             => array_merge(
                    config('constants.EMPLOYEE_TOKENS'),
                    config('constants.COMPANY_TOKENS'),
                    config('constants.CONTRACT_TOKENS'),
                    config('constants.ATTACHMENT_TOKENS'),
                    config('constants.SIGNATURE_TOKENS'),
                    config('constants.FLEX_SALARY_TOKENS')
                ),
            ];
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function create($data)
    {
        try {
            return $this->model::create($data);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getAll(array $args = [])
    {
        try {
            return $this->model::with(['employeeType'])->get();
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}