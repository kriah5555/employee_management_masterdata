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

    public function optionsFormat($data)
    {
        $response = [];
        foreach ($data as $value) {
            $response[$value['value']] = $value['label'];
        }
        return $response;
    }

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
        $formatedEmployeeDetails = [];
        foreach($employeeTypeDetails as $value) {
            $formatedEmployeeDetails[$value['id']]['id'] = $value['id'];
            $formatedEmployeeDetails[$value['id']]['name'] = $value['name'];
            $formatedEmployeeDetails[$value['id']]['category'] = $value['employee_type_category_id'];
            $formatedEmployeeDetails[$value['id']]['color'] = $value['employee_type_config']['icon_color'];
        }
        return $formatedEmployeeDetails;
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
            $planTemp = [];
            //Initializing.
            $datesArray = $dates;
            $type = $plan['employee_type_id'];
            $profile = $plan['employee_profile_id'];

            $planTemp = [
                'start_date' => $plan['start_date'],
                'start_time' => $plan['start_time'],
                'end_date' => $plan['end_date'],
                'end_date' => $plan['end_time'],
                'contract_hours' => $plan['contract_hours'],
            ];
            $response[$plan['workstation_id']]['workstation_id'] = $plan['workstation_id'];
            $response[$plan['workstation_id']]['workstation_name'] = $plan['workstation_name'];
            $response[$plan['workstation_id']]['employee'][$profile] = $employeeData[$profile];
            $response[$plan['workstation_id']]['employee'][$profile]['employee_type_name'] = $employeeTypes[$type]['name'];
            $response[$plan['workstation_id']]['employee'][$profile]['employee_type_color'] = $employeeTypes[$type]['color'];
            $response[$plan['workstation_id']]['employee'][$profile][$plan['start_date']][] = $planTemp;
        }
        return $response;
    }

    public function getWeeklyPlanningService($locations, $workstations, $employee_types, $weekNo, $year)
    {
        $response = [];
        //Weeek dates.
        $dates = getWeekDates($weekNo, $year);
        $workstationsRaw = $this->location->with('workstationsValues')->get()->toArray();
        $workstationsRaw = $this->workStationFormat($workstationsRaw);
        foreach ($workstationsRaw[$locations]['workstations'] as $value) {
            $response[$value['value']]['id'] = $value['value'];
            $response[$value['value']]['name'] = $value['label'];
            $response[$value['value']]['employee'] = [];
        }

        // dd($response);
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
                $this->employeeService->getEmployeeDetails($employeeProfiles)->toArray()
            );

            //Function details.
            // $functionDetails = $this->functionTitle->getFunctionDetails($functions);
            // dd($employeeProfilesData);

            // foreach ($employeeProfilesData as $value) {
            // }
            //Format the weekly data
            $this->formatWeeklyData($planningRaw, $employeeTypeDetails, $employeeProfilesData, $dates, $response);
            

            return $response;
            // dd($response);
            // exit;
            // print_r([$data, $employeeTypeDetails, $employeeProfilesData, $functionDetails]);
            // exit;
        }
    }

    public function getDayPlanningService()
    {
        
    }
    public function getAllPlanning()
    {

    }
}
