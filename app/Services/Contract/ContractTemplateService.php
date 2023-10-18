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

class ContractTemplateService extends BaseService
{
    protected $sector_service;

    protected $social_secretaryService;

    protected $company_service;

    protected $employee_type_service;

    public function __construct(ContractTemplate $contractTemplate)
    {
        parent::__construct($contractTemplate);
        $this->sector_service = app(SectorService::class);
        $this->social_secretaryService = app(SocialSecretaryService::class);
        $this->company_service = app(CompanyService::class);
        $this->employee_type_service = app(EmployeeTypeService::class);
    }

    public function getOptionsToCreate()
    {
        try {
            return [
                'sectors'            => $this->sector_service->getActiveSectors(),
                'social_secretaries' => $this->social_secretaryService->getSocialSecretaryOptions(),
                'companies'          => $this->company_service->getCompanyOptions(),
                'employee_types'     => $this->employee_type_service->getEmployeeTypeOptions(),
                'tokens'             => config('constants.TOKENS'),
        ];
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getOptionsToEdit($contract_template_id)
    {
        $contract_template = $this->get($contract_template_id, ['sector']);
        $companyValue = $contract_template->companyValue();
        $sectorValue = $contract_template->sectorValue();
        $employeeTypeValue = $contract_template->employeeTypeValue();
        $socialSecretaryValue = $contract_template->socialSecretaryValue();
        $options = $this->getOptionsToCreate();
        $options['details'] = $contract_template;
        $options['details']['company_value'] = $companyValue;
        $options['details']['sector_value'] = $sectorValue;
        $options['details']['employee_type_value'] = $employeeTypeValue;
        $options['details']['social_secretary_value'] = $socialSecretaryValue;

        unset($options['details']['company'], $options['details']['sector'], $options['details']['employeeType'], $options['details']['socialSecretary']);

        return $options;
    }
}