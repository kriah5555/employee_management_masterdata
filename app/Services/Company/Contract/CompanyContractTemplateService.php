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
                'employee_types' => $this->employee_type_service->getCompanyEmployeeTypes($company_id),
                'tokens'         => array_merge(
                    config('tokens.EMPLOYEE_TOKENS'),
                    config('tokens.COMPANY_TOKENS'),
                    config('tokens.CONTRACT_TOKENS'),
                    config('tokens.ATTACHMENT_TOKENS'),
                    config('tokens.SIGNATURE_TOKENS'),
                    config('tokens.FLEX_SALARY_TOKENS'),
                    config('tokens.ADDITIONAL_TOKENS'),
                    config('tokens.PLANNING_TOKENS'),
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
            $companyContractemplate = CompanyContractTemplate::create($data);
            foreach (config('app.available_locales') as $locale) {
                $companyContractemplate->setTranslation('body', $locale, $data['body'][$locale]);
            }
            $companyContractemplate->save();
            return $companyContractemplate;
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
    public function update($companyContractTemplate, $values)
    {
        try {
            $companyContractTemplate->update($values);
            return $companyContractTemplate;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}
