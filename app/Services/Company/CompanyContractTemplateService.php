<?php

namespace App\Services\Company;

use Illuminate\Support\Facades\DB;
use App\Models\Company\CompanyContractTemplate;
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
                'tokens'             => config('constants.TOKENS'),
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