<?php

namespace App\Services\Planning;

use App\Models\Planning\PlanningBase;
use App\Interfaces\Planning\PlanningInterface;
use App\Repositories\Planning\PlanningRepository;
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
        protected Location $location,
        protected Company $company,
        protected EmployeeType $employeeType,
        protected FunctionTitle $functionTitle,
        protected EmployeeService $employeeService,
        protected PlanningRepository $planningRepository
    ) {
    }

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

    public function getMonthlyPlanningService($year, $month, $location, $workstations, $employee_types)
    {
        $response = [];
        $data = $this->getMonthlyPlanningDayCount($location, $workstations, $employee_types, $month, $year);
        // $data = $this->planningBase->monthPlanning($year, $month, $location, $workstations, $employee_types);
        foreach ($data as $value) {

            $response[date('d-m-Y', strtotime($value['date']))] = $value['count'];
        }
        return $response;
    }

    public function employeeTypeFormat(array $employeeTypeDetails)
    {
        $formattedEmployeeDetails = [];
        foreach ($employeeTypeDetails as $value) {
            $formattedEmployeeDetails[$value['id']]['id'] = $value['id'];
            $formattedEmployeeDetails[$value['id']]['name'] = $value['name'];
            $formattedEmployeeDetails[$value['id']]['category'] = $value['employee_type_category_id'];
            $formattedEmployeeDetails[$value['id']]['color'] = $value['employee_type_config']['icon_color'];
        }
        return $formattedEmployeeDetails;
    }

    public function employeeProfilesFormat(array $employee)
    {
        $employeeFormat = [];
        foreach ($employee as $value) {
            $user = $value['user'];
            $name = $user['user_basic_details']['first_name'] . ' ' . $user['user_basic_details']['last_name'];
            $employeeFormat[$value['id']]['employee_id'] = $value['id'];
            // $employeeFormat[$value['id']]['user_id'] = $value['user_id'];
            // $employeeFormat[$value['id']]['social_security_number'] = $user['social_security_number'];
            $employeeFormat[$value['id']]['employee_name'] = $name;
            // $employeeFormat[$value['id']]['gender'] = $user['user_basic_details']['gender_id'];
        }
        return $employeeFormat;
    }

    // public function formatWeeklyData(array $plannings, array $employeeTypes, array $employeeData, &$response)
    public function formatWeeklyData($plannings, &$response)
    {
        foreach ($plannings as $plan) {
            $workstationId = $plan->workstation_id;
            $contractHours = $plan->contract_hours;
            $planDate = date('d-m-Y', strtotime($plan->start_date_time));
            //Initializing.
            $profile = $plan->employee_profile_id;

            //Employee details.
            if (!isset($response[$workstationId]['employee'][$profile])) {
                $response[$workstationId]['employee'][$profile] = [
                    'employee_id'   => $plan->employeeProfile->id,
                    'employee_name' => $plan->employeeProfile->user->userBasicDetails->first_name . ' ' . $plan->employeeProfile->user->userBasicDetails->last_name
                ];
                // $response[$workstationId]['employee'][$profile]['employee_type_name'] = $employeeTypes[$type]['name'];
                // $response[$workstationId]['employee'][$profile]['employee_type_color'] = $employeeTypes[$type]['color'];
                $response[$workstationId]['employee'][$profile]['total'] = [
                    'cost'           => 0,
                    'contract_hours' => 0
                ];
            }
            $planDetails = [
                "plan_id"        => $plan->id,
                "timings"        => date('H:i', strtotime($plan->start_date_time)) . ' ' . date('H:i', strtotime($plan->end_date_time)),
                "contract_hours" => $plan->contract_hours,
            ];

            if (!isset($response[$workstationId]['employee'][$profile]['plans'][$planDate])) {
                $response[$workstationId]['employee'][$profile]['plans'][$planDate]['planning'] = [];
                $response[$workstationId]['employee'][$profile]['plans'][$planDate]['contract_hours'] = 0;
                $response[$workstationId]['employee'][$profile]['plans'][$planDate]['cost'] = 0;
                $response[$workstationId]['employee'][$profile]['plans'][$planDate]['employee_type'] = [
                    'value' => $plan->employeeType->id,
                    'label' => $plan->employeeType->name,
                ];
                $response[$workstationId]['employee'][$profile]['plans'][$planDate]['function'] = [
                    'value' => $plan->functionTitle->id,
                    'label' => $plan->functionTitle->name,
                ];
            }
            $response[$workstationId]['employee'][$profile]['plans'][$planDate]['planning'][] = $planDetails;
            $response[$workstationId]['employee'][$profile]['plans'][$planDate]['contract_hours'] += $contractHours;
            $response[$workstationId]['employee'][$profile]['total']['contract_hours'] += $contractHours;
        }

        $response = array_values($response);
        foreach ($response as $id => $value) {
            $response[$id]['employee'] = array_values($value['employee']);
        }
        return $response;
    }

    public function getWeeklyPlanningService($location, $workstations, $employee_types, $weekNo, $year)
    {
        $response = [];
        //Week dates.
        $workstationsRaw = $this->location->with('workstationsValues')->get()->toArray();
        $workstationsRaw = $this->workStationFormat($workstationsRaw);
        foreach ($workstationsRaw[$location]['workstations'] as $value) {
            $response[$value['value']]['workstation_id'] = $value['value'];
            $response[$value['value']]['workstation_name'] = $value['label'];
            $response[$value['value']]['employee'] = [];
        }

        //Getting the data from the query.
        $plannings = $this->getWeeklyPlannings($location, $workstations, $employee_types, $weekNo, $year);
        if (!$plannings->isEmpty()) {
            $this->formatWeeklyData($plannings, $response);
        } else {
            $response = array_values($response);
        }
        return $response;
    }

    public function getDayPlanningService($location, $workstations, $employee_types, $date)
    {
        $plannings = $this->getDayPlannings($location, $workstations, $employee_types, $date);
        return $this->formatDayPlanning($plannings);
    }

    public function formatDayPlanning($plannings)
    {
        $response = [];
        foreach ($plannings as $plan) {
            if (!isset($response[$plan->employee_profile_id])) {
                $response[$plan->employee_profile_id] = [
                    'employee_id'   => $plan->employeeProfile->id,
                    'employee_name' => $plan->employeeProfile->user->userBasicDetails->first_name . ' ' . $plan->employeeProfile->user->userBasicDetails->last_name,
                    'plans'         => []
                ];
            }
            $response[$plan->employee_profile_id]['plans'][] = [
                'start_time'       => date('H:i', strtotime($plan->start_date_time)),
                'end_time'         => date('H:i', strtotime($plan->end_date_time)),
                'contract_hours'   => $plan->contract_hours,
                'workstation_name' => $plan->workStation->workstation_name,
                'function_name'    => $plan->functionTitle->name,
            ];
        }
        return array_values($response);
    }

    public function planningCreateOptionsService($workstation, $employeeId)
    {

    }

    public function getPlanningById($planId)
    {
        return $this->planningRepository->getPlanningById($planId);
    }

    public function getWeeklyPlannings($location, $workstations, $employee_types, $weekNumber, $year)
    {
        $weekDates = getWeekDates($weekNumber, $year);
        $startDateOfWeek = reset($weekDates);
        $endDateOfWeek = end($weekDates);
        return $this->planningRepository->getPlansBetweenDates($location, $workstations, $employee_types, $startDateOfWeek, $endDateOfWeek, '', ['workStation', 'employeeProfile.user', 'employeeType', 'functionTitle']);
    }
    public function getDayPlannings($location, $workstations, $employee_types, $date)
    {
        return $this->planningRepository->getPlansBetweenDates($location, $workstations, $employee_types, $date, $date, '', ['workStation', 'employeeProfile.user', 'employeeType', 'functionTitle']);
    }
    public function getMonthlyPlanningDayCount($location, $workstations, $employee_types, $month, $year)
    {
        $monthDates = getStartAndEndDateOfMonth($month, $year);
        return $this->planningRepository->getMonthlyPlanningDayCount($location, $workstations, $employee_types, $monthDates['start_date'], $monthDates['end_date']);
    }
}
