<?php

namespace App\Services\Company\Contract;

use App\Models\Company\Contract\CompanyContractTemplate;
use Exception;
use App\Services\EmployeeType\EmployeeTypeService;

class CompanyContractTemplateService
{
    protected $employee_type_service;

    public function __construct()
    {
        $this->employee_type_service = app(EmployeeTypeService::class);
    }

    public function getOptionsToCreate()
    {
        try {
            return [
                'employee_types' => $this->employee_type_service->getCompanyEmployeeTypes(getCurrentCompanyId()),
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
            return CompanyContractTemplate::with(['contractType'])->get();
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
    public function update($companyContractTemplate, $values)
    {
        try {
            $body = $values['body'];
            unset($values['body']);
            $companyContractTemplate->update($values);
            foreach (config('app.available_locales') as $locale) {
                $companyContractTemplate->setTranslation('body', $locale, $body[$locale]);
            }
            $companyContractTemplate->save();
            return $companyContractTemplate;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}
