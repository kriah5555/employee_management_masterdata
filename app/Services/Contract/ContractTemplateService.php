<?php

namespace App\Services\Contract;

use Illuminate\Support\Facades\DB;
use App\Models\Contract\ContractTemplate;
use App\Services\BaseService;
use Exception;
use App\Services\SocialSecretary\SocialSecretaryService;
use App\Services\Sector\SectorService;
use App\Services\CompanyService;
use App\Services\EmployeeType\EmployeeTypeService;

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
                $contractemplate = ContractTemplate::create([
                    'employee_type_id' => $values['employee_type_id'],
                ]);
                $contractemplate->socialSecretary()->sync($values['social_secretary_id'] ?? []);
                foreach (config('app.available_locales') as $locale) {
                    $contractemplate->setTranslation('body', $locale, $values['body'][$locale]);
                }
                $contractemplate->save();
                return $contractemplate;
            });
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function index()
    {
        return ContractTemplate::with('employeeType')->where('status', true)->get();
    }

    public function get($id)
    {
        return ContractTemplate::whereId($id)->with(['employeeType', 'socialSecretary'])->first();
    }

    public function update($contractTemplate, $values)
    {
        try {
            $contractTemplate->update($values);
            $contractTemplate->socialSecretary()->sync($values['social_secretary_id'] ?? []);
            return $contractTemplate;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}
