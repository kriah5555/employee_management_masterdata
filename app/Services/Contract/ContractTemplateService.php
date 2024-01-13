<?php

namespace App\Services\Contract;

use Illuminate\Support\Facades\DB;
use App\Models\Contract\ContractTemplate;
use Exception;
use App\Services\SocialSecretary\SocialSecretaryService;
use App\Services\Sector\SectorService;
use App\Services\CompanyService;
use App\Services\EmployeeType\EmployeeTypeService;
use App\Models\EmployeeType\EmployeeType;

class ContractTemplateService
{

    public function __construct(
        protected SectorService $sector_service,
        protected SocialSecretaryService $social_secretaryService,
        protected CompanyService $company_service,
        protected EmployeeTypeService $employee_type_service
    ) {
    }

    public function getOptionsToCreate()
    {
        try {
            return [
                'sectors'            => $this->sector_service->getActiveSectors(),
                'social_secretaries' => $this->social_secretaryService->getActiveSocialSecretaries(),
                'companies'          => $this->company_service->getActiveCompanies(),
                'employee_types'     => $this->employee_type_service->getActiveEmployeeTypes(),
                'tokens'             => array_merge(
                    config('tokens.EMPLOYEE_TOKENS'),
                    config('tokens.COMPANY_TOKENS'),
                    config('tokens.CONTRACT_TOKENS'),
                    config('tokens.ATTACHMENT_TOKENS'),
                    config('tokens.SIGNATURE_TOKENS'),
                    config('tokens.FLEX_SALARY_TOKENS'),
                    config('tokens.ADDITIONAL_TOKENS'),
                ),
            ];
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function create($values)
    {
        try {
            return DB::transaction(function () use ($values) {
                $contract_template = ContractTemplate::create([
                    'employee_type_id' => $values['employee_type_id'],
                ]);
                $contract_template->socialSecretary()->sync($values['social_secretary'] ?? []);
                foreach (config('app.available_locales') as $locale) {
                    $contract_template->setTranslation('body', $locale, $values['body'][$locale]);
                }
                $contract_template->save();
                return $contract_template;
            });
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function index()
    {
        return ContractTemplate::with('employeeType')->get();
    }

    public function get($id)
    {
        return ContractTemplate::whereId($id)->with(['employeeType', 'socialSecretary'])->first();
    }

    public function update($contractTemplate, $values)
    {
        try {
            $contractTemplate->update($values);
            $contractTemplate->socialSecretary()->sync($values['social_secretary'] ?? []);
            return $contractTemplate;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getTemplateForEmployeeType(EmployeeType $employeeType)
    {
        return ContractTemplate::where('employee_type_id', $employeeType->id)->first();
    }
}
