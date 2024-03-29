<?php

namespace App\Services\Company;

use App\Models\Planning\PlanningBase;
use DateTime;
use App\Models\Company\EmployeeAvailability;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\CompanyService;
use App\Models\Company\Employee\EmployeeProfile;

class EmployeeAvailabilityService
{
    public $availableDates = [];
    public $notAvailableDates = [];

    public function createAvailability($userId, $request)
    {
        try {
            foreach ($request['company_ids'] as $companyId) {
                connectCompanyDataBase($companyId);

                $employeeProfile = EmployeeProfile::where('user_id', $userId)->first();
                if ($employeeProfile) {
                    DB::connection('tenant')->beginTransaction();
                    foreach ($request['dates'] as $date) {
                        $availability = EmployeeAvailability::firstOrCreate([
                            'employee_profile_id' => $employeeProfile->id,
                            'date'                => date('Y-m-d', strtotime($date))
                        ]);
                        $availability->availability = $request['type'];
                        $availability->save();
                        if ($request['remark']) {
                            $availability->employeeAvailabilityRemarks()->create([
                                'remark' => $request['remark']
                            ]);
                        } else {
                            $availability->employeeAvailabilityRemarks()->delete();
                        }
                    }
                    DB::connection('tenant')->commit();
                }
            }
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function updateAvailability($request, $id)
    {
        try {
            DB::connection('tenant')->beginTransaction();

            $this->deleteOldDates($id);

            $groupedData = $this->dateMonthYearSeparator($request['dates']);

            $updateDateAsPerMonth = $this->dateMonthYearStore($groupedData, $request, true);

            $avalibilityTableDates = AvailabilityRemarks::where('id', $id)->update([
                'dates'       => json_encode(array_values(array_unique($request['dates']))),
                'remark'      => $request['remark'],
                'type'        => $request['type'],
                'employee_id' => $request['employee_id']
            ]);

            if (!(($updateDateAsPerMonth == "") && $avalibilityTableDates)) {
                throw new \Exception("Availability not updated, please check the dates selected!");
            }

            DB::connection('tenant')->commit();

            return "Availability updated successfully";
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function deleteAvailability($values)
    {
        try {
            DB::connection('tenant')->beginTransaction();
            $availability = EmployeeAvailability::findOrFail($values['id']);
            $availability->employeeAvailabilityRemarks()->delete();
            $availability->delete();
            DB::connection('tenant')->commit();
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function availableDates($request)
    {
        $date = $request->period;

        $carbonDate = Carbon::createFromFormat('m-Y', $date);

        $month = intval($carbonDate->format('m'));
        $year = $carbonDate->format('Y');

        $existingAvailabilityDates = EmployeeAvailability::where('year', $year)
            ->where('month', $month)
            ->where('type', 1)
            ->select('dates')
            ->get();


        if (count($existingAvailabilityDates) > 0) {
            $availableDates = $this->convertDateArray(
                $year,
                $month,
                json_decode($existingAvailabilityDates->pluck('dates')->first(), true)
            );

            $this->availableDates[] = $availableDates;
            return $availableDates;
        } else {
            $this->availableDates[] = [];
            throw new \Exception("Something went wrong!");
        }
    }

    public function notAvailableDates($request)
    {
        $date = $request->period;

        $carbonDate = Carbon::createFromFormat('m-Y', $date);

        $month = intval($carbonDate->format('m'));
        $year = $carbonDate->format('Y');

        $existingNotAvailabilityDates = EmployeeAvailability::where('year', $year)
            ->where('month', $month)
            ->where('type', 0)
            ->select('dates')
            ->get();

        if (count($existingNotAvailabilityDates) > 0) {

            $notAvialableDates = $this->convertDateArray(
                $year,
                $month,
                json_decode($existingNotAvailabilityDates->pluck('dates')->first(), true)
            );

            $this->notAvailableDates[] = $notAvialableDates;

            return $notAvialableDates;
        } else {
            $this->notAvailableDates[] = [];
            throw new \Exception("Something went wrong!");
        }
    }

    public function dateOverView($request)
    {
        $mothDates = array_merge($this->availableDates[0], $this->notAvailableDates[0]);
        $datesData = AvailabilityRemarks::where('employee_id', $request->input('employee_id'))->get();

        $matchingDates = [];

        $datesData->map(function ($value) use ($mothDates, &$matchingDates) {
            foreach (json_decode($value->dates) as $date) {
                if (in_array($date, $mothDates)) {
                    $dateObject = [
                        "employee_id" => $value->employee_id,
                        "dates"       => $date,
                        "remark"      => $value->remark,
                        "type"        => $value->type
                    ];
                    $matchingDates[] = $dateObject;
                }
            }
        });

        if (count($matchingDates) > 0) {
            return $matchingDates;
        } else {
            throw new \Exception("Something went wrong!");
        }
    }

    public function dateMonthYearStore($groupedData, $request, $update = false)
    {
        $results = "";
        global $remarkDetailsUpdate;
        $remarkDetailsUpdate = false;

        foreach ($groupedData as $yearData) {
            foreach ($yearData as $monthData) {
                $newDates = array_map('intval', $monthData['dates']);

                $existingRecord = EmployeeAvailability::where('type', $request['type'])
                    ->where('year', $monthData['year'])
                    ->where('month', $monthData['month'])
                    ->first();

                $dates = $existingRecord
                    ? $this->mergeAvailabilityDates($existingRecord, $newDates, $request, $monthData)
                    : $this->createAvailabilityData($request, $newDates, $monthData);
                if ($dates)
                    $remarkDetailsUpdate = true;
            }
        }

        if ($remarkDetailsUpdate && !$update) {
            return $this->creatingDateWithRemark($request);
        }
        return $results;
    }

    private function mergeAvailabilityDates(Availability $details, array $newDates, $request, $monthData)
    {
        $checkType = ($request['type'] == 1) ? 0 : 1;

        $datesData = EmployeeAvailability::where('type', $checkType)
            ->where('month', intval($monthData['month']))
            ->where('year', intval($monthData['year']))
            ->select('dates')
            ->get();

        $datesArray = $datesData->pluck('dates')->map(function ($value) {
            return json_decode($value, true);
        })->flatten()->toArray();

        $commonDates = array_intersect($newDates, $datesArray);

        if (count($commonDates) === 0) {
            $existingDates = json_decode($details->dates, true);
            $valuesNotInArray = array_intersect($newDates, $existingDates);

            if (empty($valuesNotInArray)) {
                $newDates = array_unique(array_merge($existingDates, $newDates));
                $details->dates = json_encode(array_values($newDates));
                $details->save();
            } else {
                throw new \Exception("Availability not created, please check the dates selected!");
            }
        } else {
            throw new \Exception("Availability not created, please check the dates selected!");
        }
        return true;
    }

    private function createAvailabilityData($request, $newDates, $monthData)
    {
        $checkType = ($request['type'] == 1) ? 0 : 1;
        $datesData = EmployeeAvailability::where('type', $checkType)
            ->select('dates')
            ->get();

        $datesArray = $datesData->pluck('dates')->map(function ($value) {
            return json_decode($value, true);
        })->flatten()->toArray();

        $commonDates = array_intersect($newDates, $datesArray);

        if (count($commonDates) == 0) {
            if (
                EmployeeAvailability::create([
                    'employee_id' => $request['employee_id'],
                    'type'        => $request['type'],
                    'year'        => $monthData['year'],
                    'month'       => $monthData['month'],
                    'dates'       => json_encode(array_values(array_unique($newDates))),
                ])
            )
                return true;
        } else {
            throw new \Exception("Availability not created, please check the dates selected!");
        }
    }

    public function dateMonthYearSeparator($dates)
    {
        $groupedData = [];

        foreach ($dates as $date) {
            $year = date("Y", strtotime($date));
            $month = date("m", strtotime($date));
            $day = date("d", strtotime($date));

            if (!isset($groupedData[$year])) {
                $groupedData[$year] = [];
            }

            if (!isset($groupedData[$year][$month])) {
                $groupedData[$year][$month] = [
                    'year'  => $year,
                    'month' => $month,
                    'dates' => [],
                ];
            }

            $groupedData[$year][$month]['dates'][] = $day;
        }

        return $groupedData;
    }

    public function creatingDateWithRemark($request)
    {
        return AvailabilityRemarks::create([
            'employee_id' => $request['employee_id'],
            'type'        => $request['type'],
            'dates'       => json_encode(array_values($request['dates'])),
            'remark'      => $request['remark']
        ]);
    }

    public function convertDateArray($year, $month, $datesValue)
    {
        $dates = [];

        foreach ($datesValue as $day) {
            $date = DateTime::createFromFormat('d-m-Y', "$day-$month-$year");

            if ($date !== false) {
                $formatted_date = $date->format('d-m-Y');
                $dates[] = $formatted_date;
            } else {
                continue;
            }
        }
        return $dates;
    }

    public function deleteOldDates($id)
    {

        $avalibilityTableDates = AvailabilityRemarks::where('id', $id)->get();

        $datesArray = $avalibilityTableDates->pluck('dates')->map(function ($value) {
            return json_decode($value, true);
        })->flatten()->toArray();

        $groupedData = $this->dateMonthYearSeparator($datesArray);

        global $deleteDates;
        $deleteDates;
        foreach ($groupedData as $yearData) {
            foreach ($yearData as $monthData) {

                $removeDate = array_map('intval', $monthData['dates']);

                $existingAvailabilityDates = EmployeeAvailability::where('year', $monthData['year'])
                    ->where('month', $monthData['month'])
                    ->where('type', $avalibilityTableDates[0]->type)
                    ->select('dates')
                    ->get();

                $oldDateArray = $existingAvailabilityDates->pluck('dates')->map(function ($value) {
                    return json_decode($value, true);
                })->flatten()->toArray();



                $dateArray = array_values(array_diff($oldDateArray, $removeDate));

                $deleteDates = EmployeeAvailability::where('year', $monthData['year'])
                    ->where('month', $monthData['month'])
                    ->where('type', $avalibilityTableDates[0]->type)
                    ->update([
                        'dates' => json_encode(array_values(array_unique($dateArray)))
                    ]);
            }
        }
        return $deleteDates;
    }

    public function getEmployeeAvailabilityForAllCompanies($userId, $period)
    {
        $availability = [
            'available_dates'     => [],
            'not_available_dates' => [],
            'both'                => [],
            'date_overview'       => [],
        ];
        $companyIds = getUserCompanies($userId);
        $dateRange = getDateRangeByPeriod($period);
        foreach ($companyIds as $companyId) {

            $company = app(CompanyService::class)->getCompanyById($companyId);
            connectCompanyDataBase($companyId);
            $employeeProfile = getEmployeeProfileByUserId($userId);
            if ($employeeProfile) {
                $existingAvailabilityDates = EmployeeAvailability::with('employeeAvailabilityRemarks')
                    ->where('employee_profile_id', $employeeProfile->id)
                    ->where('date', '>=', $dateRange['start_date'])
                    ->where('date', '<=', $dateRange['end_date'])
                    ->get();
                foreach ($existingAvailabilityDates as $existingAvailabilityDate) {
                    $date = date('d-m-Y', strtotime($existingAvailabilityDate->date));
                    if ($existingAvailabilityDate->availability) {
                        $availability['available_dates'][] = $date;
                    } else {
                        $availability['not_available_dates'][] = $date;
                    }
                    if ($existingAvailabilityDate->employeeAvailabilityRemarks) {
                        $remarkString = $existingAvailabilityDate->employeeAvailabilityRemarks->remark;
                    } else {
                        $remarkString = null;
                    }
                    if (!array_key_exists($date, $availability['date_overview'])) {
                        $availability['date_overview'][$date] = [
                            'date'         => $date,
                            'company_list' => []
                        ];
                    }
                    $availability['date_overview'][$date]['company_list'][] = [
                        'availability_id' => $existingAvailabilityDate->id,
                        'company_name'    => $company->company_name,
                        'company_id'      => $company->id,
                        'type'            => $existingAvailabilityDate->availability,
                        'remark'          => $remarkString,
                        'date'            => $date,
                    ];
                }
            }
	}
        $availability['both'] = array_values(array_intersect($availability['available_dates'], $availability['not_available_dates']));
        $availability['available_dates'] = array_values(array_diff($availability['available_dates'], $availability['both']));
        $availability['not_available_dates'] = array_values(array_diff($availability['not_available_dates'], $availability['both']));
        $availability['date_overview'] = array_values($availability['date_overview']);
        return $availability;
    }

    public function getEmployeeAvailability($employeeProfileId, $period)
    {
        $availability = [
            'available_dates'     => [],
            'not_available_dates' => [],
            'remarks'             => [],
        ];
        $dateRange = getDateRangeByPeriod($period);
        $existingAvailabilityDates = EmployeeAvailability::with('employeeAvailabilityRemarks')
            ->where('employee_profile_id', $employeeProfileId)
            ->where('date', '>=', $dateRange['start_date'])
            ->where('date', '<=', $dateRange['end_date'])
            ->get();
        foreach ($existingAvailabilityDates as $existingAvailabilityDate) {
            $date = date('d-m-Y', strtotime($existingAvailabilityDate->date));
            if ($existingAvailabilityDate->availability) {
                $availability['available_dates'][] = $date;
            } else {
                $availability['not_available_dates'][] = $date;
            }
            if ($existingAvailabilityDate->employeeAvailabilityRemarks) {
                $availability['remarks'][$date] = $existingAvailabilityDate->employeeAvailabilityRemarks->remark;
            }
        }
        return $availability;
    }
    public function getWeeklyAvailability($week, $year)
    {
        $dates = getWeekDates($week, $year);
        $startDate = date('Y-m-d 00:00:00', strtotime(reset($dates)));
        $endDate = date('Y-m-d 23:59:59', strtotime(end($dates)));
        $plannedEmployeeIds = PlanningBase::whereBetween('start_date_time', [$startDate, $endDate])->get()->pluck('employee_profile_id')->toArray();
        $plannedEmployeeIds = array_unique($plannedEmployeeIds);
        $response = [];
        foreach ($plannedEmployeeIds as $plannedEmployeeId) {
            $employeeResponse = [
                'available'     => [],
                'not_available' => [],
                'remarks'       => [],
            ];
            $availabilities = EmployeeAvailability::with('employeeAvailabilityRemarks')
                ->where('employee_profile_id', $plannedEmployeeId)
                ->where('date', '>=', $startDate)
                ->where('date', '<=', $endDate)
                ->get();
            foreach ($availabilities as $availability) {
                $date = date('d-m-Y', strtotime($availability->date));
                if ($availability->availability) {
                    $employeeResponse['available'][] = $date;
                } else {
                    $employeeResponse['not_available'][] = $date;
                }
                if ($availability->employeeAvailabilityRemarks) {
                    $employeeResponse['remarks'][$date] = $availability->employeeAvailabilityRemarks->remark;
                }
            }
            $response[$plannedEmployeeId] = $employeeResponse;
        }
        return $response;
    }
    public function getWeeklyAvailabilityForEmployee($employeeProfileId, $week, $year)
    {
        $dates = getWeekDates($week, $year);
        $startDate = date('Y-m-d 00:00:00', strtotime(reset($dates)));
        $endDate = date('Y-m-d 23:59:59', strtotime(end($dates)));
        $employeeResponse = [
            'available'     => [],
            'not_available' => [],
            'remarks'       => [],
        ];
        $availabilities = EmployeeAvailability::with('employeeAvailabilityRemarks')
            ->where('employee_profile_id', $employeeProfileId)
            ->where('date', '>=', $startDate)
            ->where('date', '<=', $endDate)
            ->get();
        foreach ($availabilities as $availability) {
            $date = date('d-m-Y', strtotime($availability->date));
            if ($availability->availability) {
                $employeeResponse['available'][] = $date;
            } else {
                $employeeResponse['not_available'][] = $date;
            }
            if ($availability->employeeAvailabilityRemarks) {
                $employeeResponse['remarks'][$date] = $availability->employeeAvailabilityRemarks->remark;
            }
        }
        return $employeeResponse;
    }
}
