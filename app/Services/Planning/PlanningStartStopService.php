<?php

namespace App\Services\Planning;

use App\Repositories\Planning\PlanningRepository;
use App\Services\Employee\EmployeeService;
use Illuminate\Support\Facades\DB;
use App\Models\Planning\TimeRegistration;
use App\Repositories\Employee\EmployeeProfileRepository;
use App\Services\Dimona\DimonaSenderService;
use App\Jobs\SendDimonaJob;
use App\Services\Company\Absence\AbsenceService;

class PlanningStartStopService
{
    public function __construct(
        protected PlanningRepository $planningRepository,
        protected EmployeeService $employeeService
    ) {
    }

    public function getEmployeePlanningCreateOptions($values)
    {
        $date = date('Y-m-d', strtotime($values['date']));
        return $this->employeeService->getEmployeeActiveTypesByDate($values['employee_id'], $date);
    }

    public function startPlanByManager($values)
    {
        DB::connection('tenant')->beginTransaction();
        $plan = $this->planningRepository->getPlanningById($values['plan_id']);
        $plan->plan_started = true;
        $plan->save();
        $timeRegistration = TimeRegistration::create([
            'plan_id'           => $plan->id,
            'actual_start_time' => date('Y-m-d H:i', strtotime($values['start_time'])),
            'status'            => true,
            'started_by'        => $values['started_by'],
            'start_reason_id'   => !empty($values['reason_id']) ? $values['reason_id'] : null
        ]);
        // app(DimonaSenderService::class)->sendDimona(getCompanyId(), $timeRegistration->id, 'IN');
        DB::connection('tenant')->commit();
        dispatch(new SendDimonaJob(getCompanyId(), $timeRegistration->id, 'IN'));
    }

    public function getPlanByQrCode($qr_data, $user_id, $start_time, $stop_time)
    {
        $qr_data = decodeData($qr_data);

        setTenantDBByCompanyId($qr_data['company_id']);

        $employee_profile = app(EmployeeProfileRepository::class)->getEmployeeProfileByUserId($user_id);
        if ($employee_profile) {
            return $this->planningRepository->getPlans('', '', $qr_data['location_id'], '', '', $employee_profile->id, [], date('d-m-Y') . ' ' . $start_time . ':00', date('d-m-Y') . ' ' . $stop_time . ':00');
        }
    }

    public function getDayPlanningToStartAndStop($location_id)
    {
        $return = [];
        if (!empty($location_id)) {
            $plannings = $this->planningRepository->getPlans('', '', $location_id, '', '', '', [], date('d-m-Y H:i'), date('d-m-Y H:i'));

            $absenceService = app(AbsenceService::class);
            $plannings->each(function ($plan) use ($absenceService, &$return) {
                if (!isset($return[$plan->workstation->id])) {
                    $return[$plan->workstation->id] = [
                        'workstation_id'   => $plan->workstation->id,
                        'workstation_name' => $plan->workstation->workstation_name,
                        'plan_list'        => [],
                    ];
                }
                
                $leaves       = $absenceService->getAbsenceForDate($plan->plan_date, config('absence.LEAVE'));
                $leave_status = $leaves->isNotEmpty();
                $return[$plan->workstation->id]['plan_list'][] = [
                    'plan_id'                  => $plan->id,
                    'plan_date'                => $plan->plan_date,
                    'employee_icon_color'      => $plan->employeeType->employeeTypeConfig->icon_color,
                    'start_time'               => $plan->start_time,
                    'end_time'                 => $plan->end_time,
                    'contract_hours'           => $plan->contract_hours,
                    'contract_hours_formatted' => $plan->contract_hours_formatted,
                    'function_id'              => $plan->function_id,
                    'function_name'            => $plan->functionTitle->name,
                    'employee_profile_id'      => $plan->employee_profile_id,
                    'employee_name'            => $plan->employeeProfile->full_name,
                    'employee_type'            => $plan->employeeType->name,
                    'employee_type'            => $plan->employeeType->name,
                    'leave_status'             => $leave_status,
                    'leave_reason'             => $leave_status ? $leaves->pluck('reason')->implode(', ') : null,
                    'leave_codes'              => $leave_status ? $leaves->pluck('absenceHours')->flatten()->pluck('holidayCode.holiday_code_name')->filter()->implode(', ') : null,
                    'plan_started'             => $plan->plan_started ?? false,
                    'break_started'            => $plan->break_started ?? false,
                    'deletable'                => $plan->timeRegistrations->isEmpty() &&!$leave_status,
                ];
            });

        }
        return array_values($return);
    }

    public function stopPlanByManager($values)
    {
        DB::connection('tenant')->beginTransaction();
        $plan = $this->planningRepository->getPlanningById($values['plan_id']);
        $plan->plan_started = false;
        $plan->save();
        $timeRegistration = $plan->timeRegistrations->last();
        $timeRegistration->actual_end_time = $this->getActualStopTime($plan->start_date_time, $values['stop_time']);
        $timeRegistration->ended_by = $values['ended_by'];
        if (!empty($values['reason_id'])) {
            $timeRegistration->stop_reason_id = $values['reason_id'];
        }
        $timeRegistration->save();
        DB::connection('tenant')->commit();
        dispatch(new SendDimonaJob(getCompanyId(), $timeRegistration->id, 'UPDATE'));
    }

    public function getActualStopTime($plan_start_date_time, $actual_stop_time)
    {
        $start_time = strtotime(date('H:i', strtotime($plan_start_date_time)));
        $end_time = strtotime($actual_stop_time);

        if ($start_time == $end_time || $start_time > $end_time) {
            $actual_stop_time = date('Y-m-d', strtotime($plan_start_date_time . '+ 1 day')) . ' ' . date('H:i', $end_time);
        } else {
            $actual_stop_time = date('Y-m-d', strtotime($plan_start_date_time)) . ' ' . date('H:i', $end_time);
        }
        return $actual_stop_time;
    }

    public function startPlanByEmployee($values)
    {
        try {
            DB::connection('tenant')->beginTransaction();

            $plans = $this->getPlanByQrCode($values['QR_code'], $values['user_id'], $values['start_time'], $values['start_time']);

            $values['plan_id'] = $plans->first()->id;

            $this->startPlanByManager($values);

            DB::connection('tenant')->commit();
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function stopPlanByEmployee($values)
    {
        try {
            DB::connection('tenant')->beginTransaction();

            $qr_data = decodeData($values['QR_code']);

            setTenantDBByCompanyId($qr_data['company_id']);

            $employee_profile = app(EmployeeProfileRepository::class)->getEmployeeProfileByUserId($values['user_id']);

            $plans = app(PlanningRepository::class)->getStartedPlanForEmployee($employee_profile->id, $qr_data['location_id']);

            $values['plan_id'] = $plans->first()->id;

            $values['plan_id'] = $plans->first()->id;

            $this->stopPlanByManager($values);

            DB::connection('tenant')->commit();
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

}
