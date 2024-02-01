<?php

namespace App\Services\Company\Absence;

use Illuminate\Support\Facades\DB;
use App\Models\Company\Absence\Absence;
use App\Services\BaseService;
use App\Repositories\Company\Absence\LeaveRepository;
use App\Services\Company\Absence\AbsenceService;
use App\Services\Holiday\HolidayCodeService;
use App\Repositories\Employee\EmployeeProfileRepository;
use Exception;
use DateTime;

class LeaveService
{
    public function __construct(protected LeaveRepository $leave_repository, protected AbsenceService $absence_service)
    {
    }

    public function getLeaves($status) # 1 => pending, 2 => approved, 3 => Rejected, 4 => Cancelled
    {
        try {
            return $this->formatLeaves($this->leave_repository->getLeaves('', $status));
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getLeavesMobile() # 1 => pending, 2 => approved, 3 => Rejected, 4 => Cancelled
    {
        try {
            return $this->formatLeaves($this->leave_repository->getLeaves(), true);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function formatLeaves($leaves, $mobile = false) # 1 => pending, 2 => approved, 3 => Rejected, 4 => Cancelled
    {
        try {
            if ($mobile) {
                return $this->absence_service->formatAbsenceDataForMobileOverview($leaves, config('absence.LEAVE'));
            } else {
                return $leaves->map(function ($leave) {
                    $leave->actions = $this->absence_service->getAbsenceActions($leave->absence_type, $leave->absence_status);

                    return $leave;
                });
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getLeaveById(string $leave_id, array $relations = [])
    {
        try {
            return $this->leave_repository->getLeaveById($leave_id, $relations);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function createLeave($details, $leave_hours, $dates_data)
    {
        try {
            $leave = $this->leave_repository->createLeave($details);

            $leave = $this->absence_service->createAbsenceRelatedData($leave, $leave_hours, $dates_data, isset($details['plan_timings']) ? $details['plan_timings'] : '');
            
            return $leave;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function updateLeave($leave_id, $updatedDetails, $leave_hours, $dates_data)
    {
        try {
            $leave = $this->getLeaveById($leave_id);

            $this->absence_service->deleteAbsenceRelatedData($leave); # delete old records of leave dates and leave hours

            $this->leave_repository->updateLeave($leave, $updatedDetails);

            $this->absence_service->createAbsenceRelatedData($leave, $leave_hours, $dates_data, $updatedDetails['plan_timings']);

            return $this->absence_service->$leave;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function updateLeaveStatus($leave_id, $status, $reason = '')
    {
        try {
            DB::connection('tenant')->beginTransaction();
            
                $leave =  $this->getLeaveById($leave_id);

                $this->absence_service->updateAbsenceStatus($leave, $status, $reason);

            DB::connection('tenant')->commit();

            return $leave;
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function applyLeave(array $details, $status = '')
    {
        try {
            DB::connection('tenant')->beginTransaction();

                $formatted_data = $this->absence_service->getAbsenceFormattedDataToSave($details, config('absence.APPROVE'));

                $leave = $this->leave_repository->createLeave($formatted_data['details']);

                $leave = $this->absence_service->createAbsenceRelatedData($leave, $formatted_data['absence_hours_data'], $formatted_data['dates_data'], $formatted_data['details']['plan_timings']);

            DB::connection('tenant')->commit();
            
            return $leave;
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function updateApprovedLeave($leave_id, array $details)
    {
        try {
            DB::connection('tenant')->beginTransaction();

                $formatted_data = $this->absence_service->getAbsenceFormattedDataToSave($details, config('absence.APPROVE'));

                $leave = $this->getLeaveById($leave_id);

                $this->absence_service->deleteAbsenceRelatedData($leave); # delete old records of leave dates and leave hours

                $this->leave_repository->updateLeave($leave, $formatted_data['details']);

                $this->absence_service->createAbsenceRelatedData($leave, $formatted_data['absence_hours_data'], $formatted_data['dates_data'], $details['plan_timings']);

                // return $leave;

            DB::connection('tenant')->commit();
            return $leave;
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function deleteLeave($leaveId)
    {
        try {
            $leave = $this->leave_repository->getLeaveById($leaveId);
            $this->absence_service->deleteAbsence($leave);
            return;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getOptionsToCreate($company_id)
    {
        try {
            return [
                'leave_codes'   => app(HolidayCodeService::class)->getCompanyLeaveCodes($company_id),
                'employees'     => app(EmployeeProfileRepository::class)->getEmployeesForHoliday(),
            ];
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function updateResponsiblePerson($values)
    {
        try {
            try {
                DB::connection('tenant')->beginTransaction();
                    $absence = $this->leave_repository->getHolidayById($values['absence_id']);
                    $this->absence_service->changeReportingManager($absence, $values['manager_id']);
                DB::connection('tenant')->commit();
            } catch (Exception $e) {
                DB::connection('tenant')->rollback();
                error_log($e->getMessage());
                throw $e;
            }
            return $absence;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}