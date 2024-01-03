<?php

namespace App\Services\Company;

use DateTime;
use App\Models\Company\EmployeeAvailability;
use App\Models\Company\EmployeeAvailabilityRemarks;
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

    public function deleteAvailability($id)
    {
        try {
            DB::connection('tenant')->beginTransaction();
            $updating_old_Dates = $this->deleteOldDates($id);

            if ($updating_old_Dates == 1) {
                $avalibilityTableDates = AvailabilityRemarks::find($id)->delete();

                if (!$avalibilityTableDates) {
                    throw new \Exception("Availability is not deleted , please check the dates selected");
                }
            } else {
                throw new \Exception("Availability is not deleted , please check the dates selected");
            }
            DB::connection('tenant')->commit();
            return "Availability deleted successfully";
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function availableDates($request)
    {
        dd($request);
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
            'date_overview'       => [],
        ];
        $companyIds = getUserCompanies($userId);
        $dateRange = getDateRangeByPeriod($period);
        foreach ($companyIds as $companyId) {

            $company = app(CompanyService::class)->getCompanyById($companyId);
            connectCompanyDataBase($companyId);
            $employeeProfile = getEmployeeProfileByUserId($userId);
            $existingAvailabilityDates = EmployeeAvailability::with('employeeAvailabilityRemarks')
                ->where('employee_profile_id', $employeeProfile->id)
                ->where('date', '>=', $dateRange['start_date'])
                ->where('date', '<=', $dateRange['end_date'])
                ->get();
            foreach ($existingAvailabilityDates as $existingAvailabilityDate) {
                if ($existingAvailabilityDate->availability) {
                    $availability['available_dates'][] = date('d-m-Y', strtotime($existingAvailabilityDate->date));
                } else {
                    $availability['not_available_dates'][] = date('d-m-Y', strtotime($existingAvailabilityDate->date));
                }
                if ($existingAvailabilityDate->employeeAvailabilityRemarks) {
                    $remarkString = $existingAvailabilityDate->employeeAvailabilityRemarks->remark;
                } else {
                    $remarkString = null;
                }
                $availability['date_overview'][] = [
                    'company' => $company->company_name,
                    'date'    => date('d-m-Y', strtotime($existingAvailabilityDate->date)),
                    'type'    => $existingAvailabilityDate->availability,
                    'remark'  => $remarkString
                ];
            }
        }
        return $availability;
    }

    public function getEmployeeAvailability($employeeProfileId, $period)
    {
        $availability = [
            'available_dates'     => [],
            'not_available_dates' => [],
        ];
        $dateRange = getDateRangeByPeriod($period);
        $existingAvailabilityDates = EmployeeAvailability::where('employee_profile_id', $employeeProfileId)
            ->where('date', '>=', $dateRange['start_date'])
            ->where('date', '<=', $dateRange['end_date'])
            ->get();
        foreach ($existingAvailabilityDates as $existingAvailabilityDate) {
            if ($existingAvailabilityDate->availability) {
                $availability['available_dates'][] = date('d-m-Y', strtotime($existingAvailabilityDate->date));
            } else {
                $availability['not_available_dates'][] = date('d-m-Y', strtotime($existingAvailabilityDate->date));
            }
        }
        return $availability;
    }
}
