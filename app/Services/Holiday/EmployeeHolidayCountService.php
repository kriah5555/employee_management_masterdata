<?php

namespace App\Services\Holiday;

use Illuminate\Support\Facades\DB;
use App\Services\Holiday\HolidayCodeService;
use App\Services\Company\Absence\AbsenceService;
use App\Models\Holiday\EmployeeHolidayCountReasons;
use App\Models\Company\Employee\EmployeeHolidayCount;
use App\Repositories\Employee\EmployeeProfileRepository;

class EmployeeHolidayCountService
{
    protected $holiday_code_service;

    protected $employeeProfileRepository;

    protected $employeeHolidayCountReasons;

    public function __construct()
    {
        $this->holiday_code_service = app(HolidayCodeService::class);
        $this->employeeProfileRepository = app(EmployeeProfileRepository::class);
        $this->employeeHolidayCountReasons = app(EmployeeHolidayCountReasons::class);
    }

    public function getAll(array $args = [])
    {
        return EmployeeHolidayCount::when(isset($args['status']) && $args['status'] !== 'all', fn($q) => $q->where('status', $args['status']))
            ->when(isset($args['employee_id']), fn($q) => $q->where('employee_id', $args['employee_id']))
            ->when(isset($args['with']), fn($q) => $q->with($args['with']))
            ->get();
    }

    public function getEmployeeHolidayCounts($employee_id, $company_id)
    {
        try {
            $companyHolidayCodes = $this->holiday_code_service->getCompanyHolidayCodes($company_id);

            $result = [];

            foreach ($companyHolidayCodes as $holidayCode) {
                $holidayCount = EmployeeHolidayCount::where('employee_id', $employee_id)
                    ->where('holiday_code_id', $holidayCode->id)
                    ->first();

                $count = $holidayCount ? $holidayCount->count : 0;
                $firstReason = $holidayCount ? $holidayCount->reasons()->where('status', 1)->first() : null; # Get the first reason with status 1 for the current employee_holiday_count_id
                $employee_holiday_count_id = $holidayCount ? $holidayCount->id : null;
                $reason = $firstReason ? $firstReason->reason : null;

                $result[] = [
                    'holiday_code_id'           => $holidayCode->id,
                    'holiday_code_name'         => $holidayCode->holiday_code_name,
                    'employee_holiday_count_id' => $employee_holiday_count_id,
                    'holiday_code_count'        => $holidayCode->count,
                    'count_type'                => config('absence.HOLIDAY_COUNT_TYPE_OPTIONS')[$holidayCode->count_type],
                    'count'                     => $holidayCode->count_type == 2 ? $count / config('constants.DAY_HOURS') : $count,
                    'reason'                    => $reason,
                ];
            }

            return $result;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }


    public function getOptionsToEdit($employee_id)
    {
        $employee_details = $this->employeeProfileRepository->getEmployeeProfileById($employee_id, ['user', 'user.userBasicDetails']);
        $company_id = getCompanyId();

        $employee_holiday_counts = $this->getEmployeeHolidayCounts($employee_id, $company_id);

        $options = [
            'employee_holiday_counts' => $employee_holiday_counts,
            'employee_details'        => $employee_details,
        ];

        return $options;
    }

    private function getExistingHolidayCodes($companyId)
    {
        return $this->holiday_code_service->model::whereHas('companies', function ($query) use ($companyId) {
            $query->where('company_id', $companyId);
        })->get();
    }

    public function create($data)
    {
        try {
            DB::connection('tenant')->beginTransaction();
            $companyId = $data['company_id'];
            $employeeId = $data['employee_id'];
            $holidayCodeCounts = $data['holiday_code_counts'];
            $existingCodes = $this->getExistingHolidayCodes($companyId)->pluck('id')->toArray();

            foreach ($holidayCodeCounts as $holidayCodeData) {
                $holidayCodeId = $holidayCodeData['holiday_code_id'];
                $holidayCode = $this->holiday_code_service->model::find($holidayCodeId);
                $count_type = $holidayCode->count_type;
                $count = $count_type == 2 ? $holidayCodeData['count'] * config('constants.DAY_HOURS') : $holidayCodeData['count'];
                $reason = $holidayCodeData['reason'];
                $existingRecord = EmployeeHolidayCount::where('employee_id', $employeeId)
                    ->where('holiday_code_id', $holidayCodeId)
                    ->first();

                if ($existingRecord) {
                    if ($existingRecord->count != $count) {
                        $newRecord = $this->createNewRecord($employeeId, $holidayCodeId, $count, 1);
                        $this->createReasonEntry($newRecord->id, $count, $reason, 1, $count_type);
                    }
                } else {
                    $newRecord = $this->createNewRecord($employeeId, $holidayCodeId, $count, 1);
                    $this->createReasonEntry($newRecord->id, $count, $reason, 1, $count_type);
                }

                // Remove the processed code from the existing codes array
                $key = array_search($holidayCodeId, $existingCodes);
                if ($key !== false) {
                    unset($existingCodes[$key]);
                }
            }

            // Set status to 0 for any remaining codes in existingCodes array
            foreach ($existingCodes as $missingCodeId) {
                $existingRecord = EmployeeHolidayCount::where('employee_id', $employeeId)
                    ->where('holiday_code_id', $missingCodeId)
                    ->first();

                if (!$existingRecord) {
                    $this->createNewRecord($employeeId, $missingCodeId, 0, 1);
                }
            }

            DB::connection('tenant')->commit();
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    private function createNewRecord($employeeId, $holidayCodeId, $count, $status)
    {
        return EmployeeHolidayCount::updateOrCreate(
            [
                'employee_id'     => $employeeId,
                'holiday_code_id' => $holidayCodeId,
            ],
            [
                'count'  => $count,
                'status' => $status,
            ]
        );
    }

    private function createReasonEntry($employeeHolidayCountId, $count, $reason, $status, $count_type)
    {
        // Set status to 0 for all existing records with the same employee_holiday_count_id
        $this->employeeHolidayCountReasons::where('employee_holiday_count_id', $employeeHolidayCountId)
            ->update(['status' => 0]);

        // Create a new reason entry with status 1
        $this->employeeHolidayCountReasons::create([
            'employee_holiday_count_id' => $employeeHolidayCountId,
            'count'                     => $count,
            'reason'                    => $reason,
            'status'                    => $status,
            'count_type'                => $count_type,
        ]);
    }

    public function getEmployeeCountHistory($employee_holiday_count_id)
    {
        try {
            $holiday_code_count_history = $this->employeeHolidayCountReasons::where('employee_holiday_count_id', $employee_holiday_count_id)->get();
            $return = [];
            foreach ($holiday_code_count_history as $data) {
                $return[] = [
                    'count'      => $data->count_type == 2 ? $data['count'] / config('constants.DAY_HOURS') : $data['count'],
                    'count_type' => config('absence.HOLIDAY_COUNT_TYPE_OPTIONS')[$data->count_type],
                    'reason'     => $data->reason,
                    'status'     => $data->status,
                ];
            }
            return $return;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getEmployeeCountOverview($employee_profile_id, $absence_type)
    {
        try {

            $absenceService = app(AbsenceService::class);
            $employee_details = $this->employeeProfileRepository->getEmployeeProfileById($employee_profile_id, ['user', 'user.userBasicDetails']);
            $company_id = getCompanyId();

            $employee_holiday_counts = collect($this->getEmployeeHolidayCounts($employee_profile_id, $company_id))->map(function ($holiday_count_details) use ($absenceService, $employee_profile_id, $absence_type) {
                $used_count = $absenceService->getEmployeeAbsenceCounts($employee_profile_id, $absence_type, $holiday_count_details['holiday_code_id']);
                if ($holiday_count_details['count']) {
                    $holiday_count_details['used_count'] = max(0, $used_count);
                    $holiday_count_details['available_count'] = max(0, $holiday_count_details['count'] - $used_count);

                    return $holiday_count_details;
                }
            })->filter()->values();



            $return = [
                'employee_holiday_counts' => $employee_holiday_counts,
                'employee_details'        => $employee_details,
            ];

            return $return;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}
