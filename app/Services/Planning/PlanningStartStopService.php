<?php

namespace App\Services\Planning;

use App\Models\Planning\PlanningBase;
use App\Repositories\Planning\PlanningRepository;
use App\Services\Employee\EmployeeService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ModelDeleteFailedException;
use App\Models\Planning\TimeRegistration;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Repositories\Employee\EmployeeProfileRepository;


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
            TimeRegistration::create([
                'plan_id'           => $plan->id,
                'actual_start_time' => date('Y-m-d H:i', strtotime($values['start_time'])),
                'status'            => true,
                'started_by'        => $values['started_by'],
                'start_reason_id'   => !empty($values['reason_id']) ? $values['reason_id'] : null
            ]);
        DB::connection('tenant')->commit();
    }

    public function getPlanByQrCode($qr_data, $user_id, $start_time, $stop_time)
    {
        $qr_data = decodeData($qr_data);

        setTenantDBByCompanyId($qr_data['company_id']);

        $employee_profile = app(EmployeeProfileRepository::class)->getEmployeeProfileByUserId($user_id);
        
        return $this->planningRepository->getPlans('', '', $qr_data['location_id'], '', '', $employee_profile->id, [], date('d-m-Y') . ' ' . $start_time . ':00', date('d-m-Y') . ' ' . $stop_time . ':00');
    }

    public function stopPlanByManager($values)
    {
        DB::connection('tenant')->beginTransaction();
            $plan = $this->planningRepository->getPlanningById($values['plan_id']);
            $plan->plan_started = false;
            $plan->save();
            $timeRegistration = $plan->timeRegistrations->last();
            $timeRegistration->actual_end_time = date('Y-m-d H:i', strtotime($values['stop_time']));
            $timeRegistration->ended_by = $values['ended_by'];
            if (!empty($values['reason_id'])) {
                $timeRegistration->stop_reason_id = $values['reason_id'];
            }
            $timeRegistration->save();
        DB::connection('tenant')->commit();
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

                $plans            = app(PlanningRepository::class)->getStartedPlanForEmployee($employee_profile->id, $qr_data['location_id']);

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
