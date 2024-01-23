<?php

namespace App\Services\Company\Absence;

use Illuminate\Support\Facades\DB;
use App\Models\Company\Absence\Absence;
use App\Services\BaseService;
use App\Repositories\Company\Absence\HolidayRepository;
use App\Services\Company\Absence\AbsenceService;
use App\Services\Holiday\HolidayCodeService;
use App\Repositories\Employee\EmployeeProfileRepository;
use Exception;
use DateTime;

class HolidayService
{
    public function __construct(protected HolidayRepository $holiday_repository, protected AbsenceService $absence_service)
    {
    }

    public function getHolidays($employee_id = '',$status = '', $employee_flow = false) # 1 => pending, 2 => approved, 3 => Rejected, 4 => Cancelled
    {
        try {
            return $this->formatHoliday($this->holiday_repository->getHolidays($employee_id, $status), $status, $employee_flow);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getHolidaysMobile() # 1 => pending, 2 => approved, 3 => Rejected, 4 => Cancelled
    {
        try {
            return $this->formatHoliday($this->holiday_repository->getHolidays(), '', false, true);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function formatHoliday($holidays, $status, $employee_flow = false, $mobile = false) # 1 => pending, 2 => approved, 3 => Rejected, 4 => Cancelled
    {
        try {
            if ($mobile) {
                return $this->absence_service->formatAbsenceDataForMobileOverview($holidays, config('absence.HOLIDAY'));
            } else {
                return $holidays->map(function ($holiday) use ($status, $employee_flow) {
                    $actions = ['approve' => false, 'reject' => false, 'change_manager' => false, 'request_cancel' => false, 'cancel' => false];
        
                    if ($employee_flow) {
                        if ($status == config('absence.PENDING')) {
                            $actions['cancel'] = true;
                        } elseif ($status == config('absence.APPROVE')) {
                            $actions['request_cancel'] = true;
                        }
                    } else {
                        if ($status == config('absence.PENDING')) {
                            $actions['approve'] = $actions['reject'] = $actions['change_manager'] = true;
                        } elseif ($status == config('absence.APPROVE') || $status == config('absence.REQUEST_CANCEL')) {
                            $actions['cancel'] = true;
                        }
                    }
                    
                    $holiday->actions = $actions;
        
                    return $holiday;
                });
            }

        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getHolidayById(string $holiday_id, array $relations = [])
    {
        try {
            return $this->holiday_repository->getHolidayById($holiday_id, $relations);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function createHoliday($details, $holiday_hours, $dates_data)
    {
        try {
            $holiday = $this->holiday_repository->createHoliday($details);

            $holiday = $this->absence_service->createAbsenceRelatedData($holiday, $holiday_hours, $dates_data);

            return $holiday;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function updateHoliday($holiday_id, $updatedDetails, $holiday_hours, $dates_data)
    {
        try {
            $holiday = $this->getHolidayById($holiday_id);

            $this->absence_service->deleteAbsenceRelatedData($holiday); # delete old records of holiday dates and holiday hours

            $this->holiday_repository->updateHoliday($holiday, $updatedDetails);

            $this->absence_service->createAbsenceRelatedData($holiday, $holiday_hours, $dates_data);

            return $holiday;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function updateHolidayStatus($holiday_id, $status, $reason = '')
    {
        try {
            DB::connection('tenant')->beginTransaction();

                $holiday =  $this->getHolidayById($holiday_id);

                $this->absence_service->updateAbsenceStatus($holiday, $status, $reason);

            DB::connection('tenant')->commit();

            return $holiday;
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function applyHoliday(array $details)
    {
        try {
            DB::connection('tenant')->beginTransaction();

                $formatted_data = $this->absence_service->getAbsenceFormattedDataToSave($details);

                $holiday = $this->holiday_repository->createHoliday($formatted_data['details']);

                $holiday = $this->absence_service->createAbsenceRelatedData($holiday, $formatted_data['absence_hours_data'], $formatted_data['dates_data']);

            DB::connection('tenant')->commit();

            return $holiday;
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function updateAppliedHoliday($holiday_id, array $details)
    {
        try {
            DB::connection('tenant')->beginTransaction();

                $formatted_data = $this->absence_service->getAbsenceFormattedDataToSave($details);

                $holiday = $this->getHolidayById($holiday_id);

                $this->absence_service->deleteAbsenceRelatedData($holiday); # delete old records of holiday dates and holiday hours

                $this->holiday_repository->updateHoliday($holiday, $formatted_data['details']);

                $this->absence_service->createAbsenceRelatedData($holiday, $formatted_data['absence_hours_data'], $formatted_data['dates_data']);

                // return $holiday;

            DB::connection('tenant')->commit();
            return $holiday;
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function deleteHoliday($absenceId)
    {
        try {
            $absence = $this->holiday_repository->getHolidayById($absenceId);
            $this->absence_service->deleteAbsence($absence);
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
                'holiday_codes' => app(HolidayCodeService::class)->getCompanyHolidayCodes($company_id),
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
                    $absence = $this->holiday_repository->getHolidayById($values['absence_id']);
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
