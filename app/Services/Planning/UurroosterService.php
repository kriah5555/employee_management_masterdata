<?php

namespace App\Services\Planning;

use App\Models\Planning\PlanningBase;
use App\Interfaces\Planning\PlanningInterface;
use App\Repositories\Planning\PlanningRepository;
use App\Services\Company\CompanyService;
use App\Services\Company\DashboardAccessService;
use App\Services\WorkstationService;
use App\Services\EmployeeFunction\FunctionService;
use App\Models\Company\Workstation;
use App\Models\Company\Location;
use App\Models\Company\Company;
use App\Models\EmployeeType\EmployeeType;
use App\Models\EmployeeFunction\FunctionTitle;
use App\Services\Employee\EmployeeService;
use App\Services\Planning\PlanningContractService;


class UurroosterService implements PlanningInterface
{

    public function __construct(
        protected DashboardAccessService $dashboardAccessService,
        protected PlanningBase $planningBase,
        protected CompanyService $companyService,
    ) {
    }
    public function getUurroosterData($dashboardToken)
    {
        $tokenData = $this->dashboardAccessService->decodeDashboardToken($dashboardToken);
        if (empty($tokenData)) {

        } else {
            $companyId = $tokenData['company_id'];
            $locationId = $tokenData['location_id'];
        }
        dd($tokenData);
    }
}
