<?php

namespace App\Services\Contract;

use Illuminate\Support\Facades\DB;
use App\Models\Contract\ContractTemplate;
use Exception;
use App\Models\EmployeeType\EmployeeType;
use App\Repositories\Company\CompanyRepository;
use App\Repositories\Planning\PlanningRepository;
use App\Models\Company\Contract\CompanyContractTemplate;
use App\Repositories\Employee\EmployeeContractRepository;

class ContractTemplateService
{

    public function __construct(
    ) {
    }

    public function getContractTemplate($contract_type_id = '', $company_id = '', $language = '')
    {
        try {
            $contract_template   = '';

            if (!empty($company_id)) { # if it is employee flow and he is trying to generate contract then there will br not tenant db set
                setTenantDBByCompanyId($company_id);
            }

            $language = empty($language) ? config('constants.DEFAULT_LANGUAGE') : $language ;

            $employee_contract_template = CompanyContractTemplate::where([
                'contract_type_id' => $contract_type_id, #contract renewal type
                'status'           => true,
            ])->get()->first();

            if (!$employee_contract_template) {
                $company = app(CompanyRepository::class)->getCompanyById(empty($company_id) ? getCompanyId() : $company_id, ['companySocialSecretaryDetails']);
                if ($company->companySocialSecretaryDetails && $company->companySocialSecretaryDetails->company_social_secretary_id) {
                    $company_social_secretary_id = $company->companySocialSecretaryDetails->social_secretary_id;
                    $employee_contract_template  = ContractTemplate::query()->with(['socialSecretary' => function ($socialSecretary) {
                        $socialSecretary->whereId('company_social_secretary_id', $company_social_secretary_id);
                    }])
                    ->where(['contract_type_id', $contract_type_id])
                    ->get()->first();
                }
            }
            
            if (!$employee_contract_template) {
                $employee_contract_template = ContractTemplate::where('contract_type_id', $contract_type_id)->get()->first();
            }

            if ($employee_contract_template) {
                $contract_template = $employee_contract_template->getTranslation('body', $language);
            }
            
            return $contract_template;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
    
    public function create($values)
    {
        try {
            return DB::transaction(function () use ($values) {
                $contract_template = ContractTemplate::create([
                    'contract_type_id' => $values['contract_type_id'],
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

    public function getAllContractTemplates($with = [])
    {
        return ContractTemplate::with($with)->get();
    }

    public function get($id)
    {
        return ContractTemplate::findOrFail($id)->with(['contractType', 'socialSecretary'])->first();
    }

    public function update($contractTemplate, $values)
    {
        try {
            $body = $values['body'];
            unset($values['body']);
            $contractTemplate->update($values);
            foreach (config('app.available_locales') as $locale) {
                $contractTemplate->setTranslation('body', $locale, $body[$locale]);
            }
            $contractTemplate->save();
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
