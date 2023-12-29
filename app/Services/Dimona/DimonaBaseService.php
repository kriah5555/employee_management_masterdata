<?php

namespace App\Services\Dimona;

use App\Models\Planning\PlanningBase;
use App\Models\Company\Employee\EmployeeContract;
use App\Models\Company\Company;

class DimonaBaseService
{
    protected $company, $employeeContract, $planningBase;

    public function __construct()
    {
        $this->company = new Company();
        $this->planningBase = new PlanningBase();
        $this->employeeContract = new EmployeeContract();
    }

    public function getPlanningById($planId)
    {
        $relations = [
            'employeeType',
            'employeeProfile',
            'employeeProfile.user',
            'employeeProfile.user.userBasicDetails',
            'timeRegistrations',
            'planningDimona',
            'planningDimona.dimonaBase',
            'planningDimona.dimonaBase.dimonaResponse'
        ];
        return $this->planningBase->with($relations)->findOrFail($planId)->toArray();
    }

    public function setCompanyData($companyId, &$dimona)
    {
        $companyData = $this->company->where('id', $companyId)->get()->toArray();
        $companyData = reset($companyData);
        return $dimona = [
            'company_id'   => $companyData['id'],
            'company_name' => $companyData['company_name'],
            'company_vat_number' => $companyData['vat_number'],
            'company_rsz_number' => $companyData['rsz_number'],
            'company_username' => $companyData['username'],
            'company_oauth' => $companyData['oauth_key'],
        ];
    }

    public function prepareDimonaDataFromPlanning($planningData, &$dimona)
    {

    }

    //employee category: 1 -> Long term, 2-> Day contract, 3 -> External
    public function initiateDimonaByPlan($companyId, $type, $plan, $registraion = '')
    {
        // dd($companyId);
        $dimona = [];

        $this->setCompanyData($companyId, $dimona);
        $planningData = $this->getPlanningById($plan);
        dd([$dimona, $planningData]);
        // $dimona['']
        if ($type == 'in') {

        } elseif ($type == 'update') {

        } elseif ($type == 'cancel') {

        } elseif ($type == 'out') {
        }
    }

    public function getContractDetails($contractId)
    {
        $relations = [
            'employeeType',
            'employeeProfile',
            'employeeProfile.user.userBasicDetails',
        ];
        $this->employeeContract->with($relations)->findOrFail($contractId);
    }


    public function initiateDimonaByContract($type, $contract)
    {

    }
}
