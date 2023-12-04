<?php

namespace App\Services;

use DateTime;
use App\Models\Company\Availability;
use App\Models\Company\AvailabilityRemarks;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class AvailabilityService
{
    public $availableDates = [];
    public $notAvailableDates = [];

    // creating availability data----note: added validation
    public function createAvailability($request)
    {
        try {
            DB::beginTransaction();
            // Create an array to store data grouped by year and month
            $groupedData = $this->dateMonthYearSeparator($request['dates']);

            return $this->dateMonthYearStore($groupedData, $request);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function dateMonthYearStore($groupedData, $request, $update = false)
    {
        // return "hiiiiii";
        $results = "";
        global $remarkDetailsUpdate;
        $remarkDetailsUpdate = false;
        // dd("ture");
        foreach ($groupedData as $yearData) {
            foreach ($yearData as $monthData) {
                $datesAsIntegers = array_map('intval', $monthData['dates']);
                // Check if the year and month already exist
                $existingRecord = Availability::where('type', $request['type'])
                    ->where('year', $monthData['year'])
                    ->where('month', $monthData['month'])
                    ->first();

                //check date which is exsisting or not
                if ($existingRecord) {

                    $checkType = ($request['type'] == 1) ? 0 : 1;
                    $datesData = Availability::where('type', $checkType)->where('month', intval($monthData['month']))->where('year', intval($monthData['year']))->select('dates')->get();

                    $datesArray = $datesData->pluck('dates')->map(function ($value) {
                        return json_decode($value, true);
                    })->flatten()->toArray();

                    $commonDates = array_intersect($datesAsIntegers, $datesArray);

                    //checking confilts date based on type?0:1
                    if (count($commonDates) <= 0) {
                        $existingDates = json_decode($existingRecord->dates, true);

                        $valuesNotInArray = array_intersect($datesAsIntegers, $existingDates);
                        // check if date is already created or not

                        if (empty($valuesNotInArray)) {

                            $newDates = array_unique(array_merge($existingDates, $datesAsIntegers));
                            $existingRecord->dates = json_encode(array_values($newDates));
                            $existingRecord->save();
                            if (!$update) $remarkDetailsUpdate = true;
                            $results = "Availability created successfully";
                        } else {
                            throw new \Exception("Availability not created, please check the dates selected!");
                        }
                    } else {
                        throw new \Exception("Availability not created, please check the dates selected!");
                    }
                } else {

                    $checkType = ($request['type'] == 1) ? 0 : 1;
                    $datesData = Availability::where('type', $checkType)->select('dates')->get();

                    $datesArray = $datesData->pluck('dates')->map(function ($value) {
                        return json_decode($value, true);
                    })->flatten()->toArray();

                    $commonDates = array_intersect($datesAsIntegers, $datesArray);

                    if (count($commonDates) == 0) {
                        Availability::create([
                            'employee_id' => $request['employee_id'],
                            'company_id' => $request['company_id'],
                            'type' => $request['type'],
                            'year' => $monthData['year'],
                            'month' => $monthData['month'],
                            'dates' => json_encode(array_values(array_unique($datesAsIntegers))),
                        ]);
                        if (!$update)  $remarkDetailsUpdate = true;
                        $results = "Availability created successfully";
                    } else {
                        throw new \Exception("Availability not created, please check the dates selected!");
                    }
                }
            }
        }
        if ($remarkDetailsUpdate) {


            $this->creatingDateWithRemark($request);

            Availability::where('company_id', 1)->get();
        }
        return $results;
    }


    public function dateMonthYearSeparator($dates)
    {
        $groupedData = [];

        foreach ($dates as $date) {
            $year = date("Y", strtotime($date));
            $month = date("m", strtotime($date));
            $day = date("d", strtotime($date));

            // Create an array for the year if it doesn't exist
            if (!isset($groupedData[$year])) {
                $groupedData[$year] = [];
            }

            // Create an array for the month if it doesn't exist
            if (!isset($groupedData[$year][$month])) {
                $groupedData[$year][$month] = [
                    'year' => $year,
                    'month' => $month,
                    'dates' => [],
                ];
            }

            // Add the day to the month's 'dates' array
            $groupedData[$year][$month]['dates'][] = $day;
        }

        return $groupedData;
    }

    public function creatingDateWithRemark($request)
    {
        AvailabilityRemarks::create([
            'employee_id' => $request['employee_id'],
            'company_id' => $request['company_id'],
            'type' => $request['type'],
            'dates' => json_encode(array_values($request['dates'])),
            'remark' => $request['remark']
        ]);
    }

    //convert date format
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

    //Available date Function
    public function avilableDates($request)
    {
        $date = $request->period;

        $carbonDate = Carbon::createFromFormat('m-Y', $date);

        $month = intval($carbonDate->format('m'));
        $year = $carbonDate->format('Y');

        $existingAvailabilityDates = Availability::where('year', $year)
            ->where('month', $month)
            ->where('type', 1)
            ->select('dates')
            ->get();

        // dd($existingAvailabilityDates);
        if (count($existingAvailabilityDates) > 0) {
            $avilableDates = $this->convertDateArray(
                $year,
                $month,
                json_decode($existingAvailabilityDates->pluck('dates')->first(), true)
            );

            $this->availableDates[] = $avilableDates;
            return $avilableDates;
        } else {
            $this->availableDates[] = [];
            throw new \Exception("Something went wrong!");
        }
    }

    // Not Availability Dates
    public function notAvailableDates($request)
    {
        $date = $request->period;

        $carbonDate = Carbon::createFromFormat('m-Y', $date);

        $month = intval($carbonDate->format('m'));
        $year = $carbonDate->format('Y');

        // $year = date("Y", strtotime($date));
        // $month = date("m", strtotime($date));

        $existingNotAvailabilityDates = Availability::where('year', $year)
            ->where('month', $month)
            ->where('type', 0)
            ->select('dates')
            ->get();

        if (count($existingNotAvailabilityDates) > 0) {

            $notAvilableDates = $this->convertDateArray(
                $year,
                $month,
                json_decode($existingNotAvailabilityDates->pluck('dates')->first(), true)
            );

            $this->notAvailableDates[] = $notAvilableDates;

            return $notAvilableDates;
        } else {
            $this->notAvailableDates[] = [];
            throw new \Exception("Something went wrong!");
        }
    }

    //all date overView in the particular month
    public function dateOverView($request)
    {

        $mothDates = array_merge($this->availableDates[0], $this->notAvailableDates[0]);

        // print_r($mothDates);

        $datesData = AvailabilityRemarks::where('employee_id', $request->employee_id)->get();

        $matchingDates = [];

        $datesData->map(function ($value) use ($mothDates, &$matchingDates) {
            foreach (json_decode($value->dates) as $date) {
                if (in_array($date, $mothDates)) {
                    $dateObject = [
                        "employee_id" => $value->employee_id,
                        "company_id" => $value->company_id,
                        "dates" => $date,
                        "remark" => $value->remark,
                        "type" => $value->type
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


    //updates things
    public function updateMonthDates($request, $id)
    {
        try {
            DB::beginTransaction();

            $updating_old_Dates = $this->deleteOldDates($id);

            $groupedData = $this->dateMonthYearSeparator($request['dates']);

            $updateDateAsPerMonth = $this->dateMonthYearStore($groupedData, $request, true);

            $avalibilityTableDates = AvailabilityRemarks::where('id', $id)->update([
                'dates' => json_encode(array_values(array_unique($request['dates']))),
                'company_id' => $request['company_id'],
                'remark' => $request['remark'],
                'type' => $request['type'],
                'employee_id' => $request['employee_id']
            ]);


            if ($updateDateAsPerMonth && $avalibilityTableDates) {
                return "Availability updated successfully";
            } else {
                throw new \Exception("Availability not updated, please check the dates selected!");
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    //deleting dates in the particular month
    public function deleteAvailability($request)
    {

        $updating_old_Dates = $this->deleteOldDates($request->availability_id);

        if ($updating_old_Dates == 1) {
            $avalibilityTableDates = AvailabilityRemarks::find($request->availability_id);

            if ($avalibilityTableDates) {
                $avalibilityTableDates->delete();
                return "Availability deleted successfully";
            } else {
                throw new \Exception("Availability is not deleted , please check the dates selected");
            }
        } else {
            throw new \Exception("Availability is not deleted , please check the dates selected");
        }
    }

    //helper function for deleting old dates in DB

    // Updating  issue in Avilability table

    public function deleteOldDates($id)
    {
        $avalibilityTableDates = AvailabilityRemarks::where('id', $id)->get();

        $datesArray = $avalibilityTableDates->pluck('dates')->map(function ($value) {
            return json_decode($value, true);
        })->flatten()->toArray();

        $groupedData = $this->dateMonthYearSeparator($datesArray);

        // print_r($avalibilityTableDates[0]->type);
        global $deleteDates;
        $deleteDates;
        foreach ($groupedData as $yearData) {
            foreach ($yearData as $monthData) {

                //code is removing previous dates before updatings

                // $removeDated = ($monthData['dates']);
                $removeDated = array_map('intval', $monthData['dates']);

                $existingAvailabilityDates = Availability::where('year', $monthData['year'])
                    ->where('month', $monthData['month'])
                    ->where('type', $avalibilityTableDates[0]->type)
                    ->select('dates')
                    ->get();

                // print_r($existingAvailabilityDates);

                $oldDateArray = $existingAvailabilityDates->pluck('dates')->map(function ($value) {
                    return json_decode($value, true);
                })->flatten()->toArray();

                $dateArray = array_values(array_diff($oldDateArray, $removeDated));

                // updating old values before updating the corrent value
                $deleteDates = Availability::where('year', $monthData['year'])
                    ->where('month', $monthData['month'])
                    ->where('type', $avalibilityTableDates[0]->type)
                    ->update([
                        'dates' => json_encode(array_values(array_unique($dateArray)))
                    ]);
            }
        }
        return $deleteDates;
    }
}
