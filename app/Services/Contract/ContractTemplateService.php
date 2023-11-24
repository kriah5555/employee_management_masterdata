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
        $this->sector_service          = app(SectorService::class);
        $this->social_secretaryService = app(SocialSecretaryService::class);
        $this->company_service         = app(CompanyService::class);
        $this->employee_type_service   = app(EmployeeTypeService::class);
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
                    config('constants.EMPLOYEE_TOKENS'),
                    config('constants.COMPANY_TOKENS'),
                    config('constants.CONTRACT_TOKENS'),
                    config('constants.ATTACHMENT_TOKENS'),
                    config('constants.SIGNATURE_TOKENS')
                ),
            ];
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getOptionsToEdit($contract_template_id)
    {
        try {
            $contract_template  = $this->get($contract_template_id, ['company', 'employeeType', 'company', 'socialSecretary']);
            $contract_template->socialSecretary;
            $options            = $this->getOptionsToCreate();
            $options['details'] = $contract_template;
            return $options;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}