<?php

namespace App\Services\Planning;

use App\Models\Planning\PlanningBase;
use App\Interfaces\Planning\PlanningInterface;
use App\Services\WorkstationService;
use App\Services\EmployeeFunction\FunctionService;
use App\Models\Company\Workstation;
use App\Models\Company\Location;
use App\Models\Company\Company;
use App\Models\EmployeeType\EmployeeType;
use App\Models\EmployeeFunction\FunctionTitle;
use App\Services\Employee\EmployeeService;


class PlanningService implements PlanningInterface
{

    public function __construct(
        protected PlanningBase $planningBase,
        protected Workstation $wf,
        protected Location $location,
        protected Company $company,
        protected EmployeeType $employeeType,
        protected FunctionTitle $functionTitle,
        protected EmployeeService $employeeService
    ) {}

    /**
     * Get employee type of the company
     *
     * @param  [type] $companyId
     * @return array
     */
    public function getEmployeeTypes($companyId)
    {
        $response = [];
        $employeeTypes = [];
        $data = $this->company->employeeTypes($companyId)->get()->toArray();

        if (count($data) > 0) {
            $data = reset($data);
            foreach ($data['sectors'] as $values) {
                $response += $values['employee_types_value'];
            }
        }
        return $response;
    }

    /**
     * Formating the options
     *
     * @param  [type] $data
     * @return array
     */
    public function optionsFormat($data)
    {
        $response = [];
        foreach ($data as $value) {
            $response[$value['value']] = $value['label'];
        }
        return $response;
    }

    /**
     * Workstations data format.
     *
     * @param  [type] $data
     * @return array
     */
    public function workStationFormat($data) 
    {
        $response = [];
        foreach ($data as $value) {
            $response[$value['id']]['id'] = $value['id'];
            $response[$value['id']]['name'] = $value['location_name'];
            $response[$value['id']]['workstations'] = $value['workstations_values'];
        }
        return $response;
    }

    /**
     * Function indexing by id
     *
     * @param  [type] $data
     * @return array
     */
    public function functionFormat($data)
    {
        $response = [];
        foreach ($data as $value) {
            $response[$value['id']] = $value;
        }
        return $response;
    }

    public function getPlanningOverviewFilterService($companyId)
    {
        $output['locations'] = $this->location->all(['id as value', 'location_name as label'])->toArray();
        $output['workstations'] = $this->location->with('workstationsValues')->get()->toArray();

        $response['locations'] = $this->optionsFormat($output['locations']);
        $response['workstations'] = $this->workStationFormat($output['workstations']);
        $response['employee_types'] = $this->getEmployeeTypes($companyId);

        return $response;
    }

    public function getMonthlyPlanningService($year, $locations, $workstations, $employee_types)
    {
        $response = [];
        $data = $this->planningBase->monthPlanning($year, $locations, $workstations, $employee_types);
        foreach ($data as $value) {
            $response[$value['date']] = $value['count'];
        }
        return $response;
    }

    public function employeeTypeFormat(array $employeeTypeDetails)
    {
        $formattedEmployeeDetails = [];
        foreach($employeeTypeDetails as $value) {
            $formattedEmployeeDetails[$value['id']]['id']       = $value['id'];
            $formattedEmployeeDetails[$value['id']]['name']     = $value['name'];
            $formattedEmployeeDetails[$value['id']]['category'] = $value['employee_type_category_id'];
            $formattedEmployeeDetails[$value['id']]['color']    = $value['employee_type_config']['icon_color'];
        }
        return $formattedEmployeeDetails;
    }

    public function employeeProfilesFormat(array $employee)
    {
        $employeeFormat = [];
        foreach($employee as $value) {
            $user = $value['user'];
            $name = $user['user_basic_details']['first_name']. ' ' . $user['user_basic_details']['last_name'];
            $employeeFormat[$value['id']]['id'] = $value['id'];
            $employeeFormat[$value['id']]['user_id'] = $value['user_id'];
            $employeeFormat[$value['id']]['social_security_number'] = $user['social_security_number'];
            $employeeFormat[$value['id']]['employee_name'] = $name;
            $employeeFormat[$value['id']]['gender'] = $user['user_basic_details']['gender_id'];
        }
        return $employeeFormat;
    }

    public function formatWeeklyData(array $plannings, array $employeeTypes, array $employeeData, array $dates, &$response)
    {
        foreach ($plannings as $plan) 
        {
            //Initializing.
            $type = $plan['employee_type_id'];
            $profile = $plan['employee_profile_id'];

            //Workstations details.
            if (!isset($response[$plan['workstation_id']])) {
                $response[$plan['workstation_id']]['workstation_id'] = $plan['workstation_id'];
                $response[$plan['workstation_id']]['workstation_name'] = $plan['workstation_name'];
            }

            //Employee details.
            if (!isset($response[$plan['workstation_id']]['employee'][$profile])) {
                $response[$plan['workstation_id']]['employee'][$profile] = $employeeData[$profile];
                $response[$plan['workstation_id']]['employee'][$profile]['employee_type_name'] = $employeeTypes[$type]['name'];
                $response[$plan['workstation_id']]['employee'][$profile]['employee_type_color'] = $employeeTypes[$type]['color'];
                foreach ($dates as $date) {
                    $response[$plan['workstation_id']]['employee'][$profile][$date] = [];
                }
                $response[$plan['workstation_id']]['employee'][$profile]['total'] = 0;
            }

            $planTemp = [];
            $planTemp = [
                'start_date' => $plan['start_date'],
                'start_time' => $plan['start_time'],
                'end_date' => $plan['end_date'],
                'end_date' => $plan['end_time'],
                'contract_hours' => $plan['contract_hours'],
            ];

            $response[$plan['workstation_id']]['employee'][$profile][$plan['start_date']][] = $planTemp;
            $response[$plan['workstation_id']]['employee'][$profile]['total'] += $plan['contract_hours'];
        }

        $response = array_values($response);
        foreach ($response as $id => $value) {
            $response[$id]['employee'] = array_values($value['employee']);
        }
        return $response;
    }

    public function getWeeklyPlanningService($locations, $workstations, $employee_types, $weekNo, $year)
    {
        $response = [];
        //Week dates.
        $dates = getWeekDates($weekNo, $year);
        $workstationsRaw = $this->location->with('workstationsValues')->get()->toArray();
        $workstationsRaw = $this->workStationFormat($workstationsRaw);
        foreach ($workstationsRaw[$locations]['workstations'] as $value) {
            $response[$value['value']]['id'] = $value['value'];
            $response[$value['value']]['name'] = $value['label'];
            $response[$value['value']]['employee'] = [];
        }

        //Getting the data from the query.
        $planningRaw = $this->planningBase->weeklyPlanning($locations, $workstations, $employee_types, $weekNo, $year);
        if (count($planningRaw) > 0) {
            $functions = array_unique(array_column($planningRaw, 'function_id'));
            $employeeTypes = array_unique(array_column($planningRaw, 'employee_type_id'));
            $employeeProfiles = array_unique(array_column($planningRaw, 'employee_profile_id'));

            //Employee type details.
            $employeeTypeDetails = $this->employeeTypeFormat(
                $this->employeeType->getEmployeeTypeDetails($employeeTypes)
            );

            //Employee profiles.
            $employeeProfilesData = $this->employeeProfilesFormat(
                $this->employeeService->getEmployeeDetailsPlanning($employeeProfiles)->toArray()
            );

            //Function details.
            // $functionDetails = $this->functionTitle->getFunctionDetails($functions);

            //Format the weekly data
            $this->formatWeeklyData($planningRaw, $employeeTypeDetails, $employeeProfilesData, $dates, $response);
        }
        return $response;
    }

    public function getDayPlanningService($locations, $workstations, $employee_types, $date)
    {
        $response = [];
        $planningRaw = $this->planningBase->dayPlanning($locations, $workstations, $employee_types, $date);
        
        $plannings = (count($planningRaw->all()) > 0) ? $planningRaw->toArray() : [];
        // getting required info.
        if (count($plannings) > 0) {
            $workstations = array_unique(array_column($plannings, 'workstation_id'));
            $employeeTypes = array_unique(array_column($plannings, 'employee_type_id'));
            $employeeProfiles = array_unique(array_column($plannings, 'employee_profile_id'));
            $functions = array_unique(array_column($plannings, 'function_id'));
    
            //Employee type details.
            $employeeTypeDetails = $this->employeeTypeFormat(
                $this->employeeType->getEmployeeTypeDetails($employeeTypes)
            );
    
            //Employee profiles.
            $employeeProfilesData = $this->employeeProfilesFormat(
                $this->employeeService->getEmployeeDetailsPlanning($employeeProfiles)->toArray()
            );

            //Function details.
            $functionDetails = $this->functionFormat($this->functionTitle->getFunctionDetails($functions));
        }

        foreach ($planningRaw->all() as $planning) {
            $this->formatDayPlanning($planning, $employeeProfilesData, $functionDetails, $employeeTypeDetails, $response);
        }
        return $response;
    }

    public function formatDayPlanning($planningBase, $employeeInfo, $functionInfo, $employeeTypeInfo, &$response)
    {
        // $array = $planningBase->toArray();
        $data = [];
        $planningId = $planningBase->id;
        $data['id'] = $planningBase->id;
        $data['location_id'] = $planningBase->location_id;
        $data['workstation_id'] = $planningBase->workstation_id;
        $data['function_id'] = $planningBase->function_id;
        $data['employee_type_id'] = $planningBase->employee_type_id;
        $data['employee_profile_id'] = $planningBase->employee_profile_id;
        $data['start_date_time'] = $planningBase->start_date_time;
        $data['end_date_time'] = $planningBase->end_date_time;
        $data['function'] = ['id' => $planningBase->function_id, 'name' => $functionInfo[$planningBase->function_id]['name']];
        $data['employee_type'] = $employeeTypeInfo[$planningBase->employee_type_id];
        $data['employee_details'] = $employeeInfo[$planningBase->employee_profile_id];

        // Getting information from relations.
        //    $location = $planningBase->location->toArray();
        //    $workstation = $planningBase->workStation->toArray();
        //    $employeeProfile = $planningBase->employeeProfile->toArray();

        $timeRegistrations = $planningBase->timeRegistrations->toArray();
        // Accessing data from TimeRegistration models
        foreach ($timeRegistrations as $timeRegistration) {
            $startTime = $timeRegistration['actual_start_time'];
            $endTime = $timeRegistration['actual_end_time'];
        }

        $contracts = $planningBase->contracts->toArray();
        // Accessing data from PlanningContracts models
        foreach ($contracts as $contract) {
            $contractStatus = $contract['contract_status'];
            // Access other properties as needed
        }

        $breaks = $planningBase->breaks->toArray();
        // Accessing data from PlanningBreak models
        foreach ($breaks as $break) {
            $breakStartTime = $break['break_start_time'];
            $breakEndTime = $break['break_end_time'];
        }
        $data['actions'] = [
            'plan' => ['action' => 'start', 'status' => 'active'],
            'contract' => ['action' => 'sign', 'status' => 'active'],
            'break' => ['actions' => 'start', 'status' => 'active'],
        ];
        $response[]= $data;
    }


    public function planningCreateOptionsService($workstation, $employeeId)
    {

    }
}
