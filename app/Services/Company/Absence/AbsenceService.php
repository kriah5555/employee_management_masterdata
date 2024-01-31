<?php

namespace App\Services\Company\Absence;

use Illuminate\Support\Facades\DB;
use App\Models\Company\Absence\Absence;
use App\Models\Company\Absence\AbsenceDates;
use App\Repositories\Company\CompanyRepository;
use App\Repositories\Planning\PlanningRepository;
use Exception;
use DateTime;

class AbsenceService
{
    public function deleteAbsenceRelatedData(Absence $absence)
    {
        try {
            $absence->absenceHours()->delete(); # Delete related AbsenceHours

            $absence->absenceDates()->delete(); # Delete related AbsenceDates

            return ;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function createAbsenceRelatedData(Absence $absence, $absence_hours, $dates_data, $plan_timings = [])
    {
        try {
            $absence->absenceHours()->createMany($absence_hours);

            $absence->absenceDates()->create($dates_data);

            if (!empty($plan_timings)) {
                $plan_ids = $this->getPlanIdsForTimings($dates_data['dates'], $plan_timings, $absence->employee_profile_id);
                // $plan_ids = $this->getPlanIdsForTimings(json_decode($dates_data['dates']), $plan_timings, $absence->employee_profile_id);
                $absence->plans()->sync($plan_ids);
            }

            return $absence;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getPlanIdsForTimings($dates, $timings, $employee_profile_id)
    {
        if (isset($dates['from_date']) && isset($dates['to_date'])) {
            $absence_applied_dates = getDatesArray($dates['from_date'], $dates['to_date']);
        } else {
            $absence_applied_dates = $dates;
        }

        $date_times = array_map(function ($absence_applied_date) use ($timings) {
            return array_map(function ($timing) use ($absence_applied_date) {
                $time = explode('-', explode(' ', $timing)[0]);
                return [
                    'start_date_time' => $absence_applied_date . ' ' . $time[0] . ':00',
                    'end_date_time'   => $absence_applied_date . ' ' . $time[1] . ':00',
                ];
            }, $timings);
        }, $absence_applied_dates);

        $flatDateTimes = array_merge(...$date_times);

        return app(PlanningRepository::class)
            ->getPlanningsForTimings($employee_profile_id, $flatDateTimes)
            ->pluck('id');
    }

    public function updateAbsenceStatus(Absence $absence, $status, $reason = '')
    {
        try {
                if (array_key_exists($status, config('absence.STATUS'))) {
                    $data['absence_status'] = $status;
                    if (!empty($reason)) {
                        $data['reason'] = $status;
                    }
                    $absence->update($data);
                }
                return $absence;
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getAbsenceFormattedDataToSave($details, $absence_status = '')
    {
        $absence_status            = $absence_status != '' ? $absence_status : config('absence.PENDING');
        $details['absence_status'] = $absence_status;
        $holiday_code_counts       = $details['holiday_code_counts'];
        $duration_type             = $details['duration_type'];
        $absence_hours_data        = [];

        foreach ($holiday_code_counts as $index => $holiday_code_count) {
            $absence_hours_data[$index] = [
                'holiday_code_id' => $holiday_code_count['holiday_code'],
                'hours'           => $this->getCalculateAbsenceHours($details, $holiday_code_count),
                'duration_type'   => (in_array($holiday_code_count['duration_type'], [config('absence.FIRST_HALF'), config('absence.SECOND_HALF')])
                                    && in_array($duration_type, [config('absence.MULTIPLE_HOLIDAY_CODES_FIRST_HALF'), config('absence.MULTIPLE_HOLIDAY_CODES_SECOND_HALF'), config('absence.FIRST_AND_SECOND_HALF')])) # if the duration type is forts and second half with multiple holiday codes or  it is first and second half
                                    ? $holiday_code_count['duration_type'] : $duration_type,
            ];

            if ($this->breakHolidayCodeCountLoopCondition($index, $duration_type)) {
                break;
            }
        }

        $dates_data = [
            'dates'      => $details['dates'],
            'dates_type' => $details['duration_type'] == config('absence.MULTIPLE_DATES') ? config('absence.DATES_FROM_TO') : config('absence.DATES_MULTIPLE')
        ];

        return [
            'absence_hours_data' => $absence_hours_data,
            'dates_data'         => $dates_data,
            'details'            => $details
        ];
    }

    public function breakHolidayCodeCountLoopCondition($index, $duration_type)
    {
        return 
            in_array($duration_type, [
                config('absence.MULTIPLE_DATES'), # if multiple dates it will have from and to date so only one holiday code
                config('absence.FIRST_HALF'), # if first half it will have single code
                config('absence.SECOND_HALF'), # if second half it will have single code
                config('absence.FULL_DAYS'), # if full day it will have single code
            ])
            || ($index == 2 && config('absence.FIRST_AND_SECOND_HALF')); # if it is first nd second half then there will be two holiday codes
    }

    public function getCalculateAbsenceHours($details, $holiday_code_count, $with_date_calculates = false) # if $with_date_calculates => true it will return the hours * days else will return hours
    {
        $hours         = 0;
        $duration_type = $details['duration_type'];
        $days          = $with_date_calculates ? count($details['dates']) : 1;
        if ($duration_type == config('absence.MULTIPLE_DATES')) { # if form and to dates are given single holiday code
            $fromDate = new DateTime($details['dates']['from_date']);
            $toDate   = new DateTime($details['dates']['to_date']);
            $interval = $fromDate->diff($toDate);
            $days     = $with_date_calculates ? $interval->days + 1 : 1;
            $hours    = config('constants.DAY_HOURS');
        } elseif ($duration_type == config('absence.FIRST_AND_SECOND_HALF')) { # there will be only one holiday code
            $hours = (config('constants.DAY_HOURS') / 2);
        } elseif (in_array($duration_type, [config('absence.MULTIPLE_HOLIDAY_CODES')])) { # if it is multiple codes the use the hours provided by user
            $hours = $holiday_code_count['hours'];
        } elseif (in_array($duration_type, [config('absence.MULTIPLE_HOLIDAY_CODES_FIRST_HALF'), config('absence.MULTIPLE_HOLIDAY_CODES_SECOND_HALF')])) { # if it is multiple holiday codes with first or second half
            $hours = ($holiday_code_count['duration_type'] == '') ? $holiday_code_count['hours']: (config('constants.DAY_HOURS') / 2); # if duration type is set in the holiday codes as first or second half the use default hours else use hours provided by user
        } elseif (in_array($duration_type, [config('absence.FIRST_HALF'), config('absence.SECOND_HALF')])) { # will holiday code with no hours
            $hours = (config('constants.DAY_HOURS') / 2);
        } elseif (in_array($duration_type, [config('absence.FULL_DAYS')])) { # will fll day will have only one holiday coe with on hours
            $hours = config('constants.DAY_HOURS');
        }
        return $hours * $days;
    }

    public function deleteAbsence(Absence $absence)
    {
        $absence->absenceHours()->delete();
        $absence->absenceDates()->delete();
        $absence->delete();
        return;
    }

    public function changeReportingManager($absence, $responsible_person_id)
    {
        return $absence->update(['manager_id' => $responsible_person_id]);
    }

    public function getAbsenceDetailsForWeek($week_number, $year)
    {
        try {
            $dates  = getWeekDates($week_number, $year, 'd-m-Y');
            $return = array_fill_keys($dates, [
                'leaves'         => [],
                'holidays'       => [],
                'public_holiday' => [],
            ]);

            $company_repository = app(CompanyRepository::class);

            
            foreach ($dates as $date) {
                $absences = $this->getAbsenceForDate($date);
                foreach ($absences as $absence) {
                    $return[$date][$absence->absence_type == config('absence.Holiday') ? 'holidays' : 'leaves'][] = $this->formatAbsenceDataForOverview($absence);
                }


                $public_holiday = $company_repository->getCompanyPublicHolidays(getCompanyId(), [date('Y-m-d', strtotime($date))])->first();
                if ($public_holiday) {
                    $return[$date]['public_holiday'] = [
                        'public_holidays_id'   => $public_holiday->id,
                        'public_holidays_name' => $public_holiday->name,
                        'date'                 => $public_holiday->date,
                    ];
                }
            }

            return $return;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getAbsenceForDate($date, $absence_type = '', $employee_profile_id = '') #$absence_type = [1 => Holiday, 2 -> Leave]
    {
        try {
            $absenceIds = AbsenceDates::where(function ($query) use ($date) {
                $query->where('dates_type', config('absence.DATES_MULTIPLE')) // Multiple dates
                    ->whereJsonContains('dates', $date);
            })
            ->orWhere(function ($query) use ($date) {
                    $results = $query->where('dates_type', config('absence.DATES_FROM_TO')) // From and To date
                    ->get()
                    ->filter(function ($absence) use ($date) {
                        $dates = $absence->dates;
                        return strtotime($date) >= strtotime($dates['from_date']) && strtotime($date) <= strtotime($dates['to_date']);
                    });

                    if ($results->isEmpty()) {
                        $query->whereRaw('false');
                    }
                })
            ->pluck('absence_id');
            return Absence::whereIn('id', $absenceIds)
                ->when(!empty($absence_type), function ($query) use ($absence_type) {
                    $query->where('absence_type', $absence_type);
                })
                ->when(!empty($employee_profile_id), function ($query) use ($employee_profile_id) {
                    $query->where('employee_profile_id', $employee_profile_id);
                })
                ->get();
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function formatAbsenceDataForOverview($absence)
    {
        try {
            $duration_type       = $absence->duration_type;
            $holiday_code_counts = [];
            $return = [
                'id' => $absence->id,
                'duration_type'          => $duration_type,
                'half_day'               => ($duration_type == config('absence.FIRST_HALF') || $duration_type == config('absence.SECOND_HALF')),
                'first_half'             => $duration_type == config('absence.FIRST_HALF'),
                'second_half'            => $duration_type == config('absence.SECOND_HALF'),
                'multiple_days'          => $duration_type == config('absence.MULTIPLE_DATES'),
                'multiple_holiday_codes' => $duration_type == config('absence.MULTIPLE_HOLIDAY_CODES'),
                'dates'                  => $absence->absenceDates->dates,
                'reason'                 => $absence->reason,
                'plan_timings'           => $absence->plan_timings,
                'plan_ids'               => $absence->plan_ids,
                'shift_leave'            => !empty($absence->plan_ids),
                'employee'               => [
                    'value' => $absence->employee_profile_id,
                    'label' => $absence->employee->full_name,
                ],
                'manager' => [
                    'value' => ($absence->manager) ? $absence->manager->id : null,
                    'label' => ($absence->manager) ? $absence->manager->full_name : null,
                ]
            ];

            foreach ($absence->absenceHours as $absence_holiday_hours_detail) {
                $holiday_code_counts[] = [
                    'holiday_code_id'   => $absence_holiday_hours_detail->holiday_code_id, 
                    'holiday_code_name' =>$absence_holiday_hours_detail->holidayCode->holiday_code_name, 
                    'hours'             => $absence_holiday_hours_detail->hours, 
                    'duration_type'     => $absence_holiday_hours_detail->duration_type
                ];
            }

            $return['holiday_code_counts'] = $holiday_code_counts;
            return $return;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getEmployeeAbsenceCounts($employee_profile_id, $absence_type, $holiday_code_id) # Holiday or leave
    {
        $absences = Absence::with(['absenceHours' => function ($query) use ($holiday_code_id) {
            $query->select('absence_id', 'hours')
                ->where('holiday_code_id', $holiday_code_id)
                ->where('hours', '>', 0);
            }])
            ->where('absence_type', $absence_type)
            ->where('employee_profile_id', $employee_profile_id);

        $absences = $absences->get(['id', 'employee_profile_id']);

        $employee_absence_hours_used = 0;

        foreach ($absences as $absence) {
            $employee_absence_hours_used = $absence->absenceDates ?  count($absence->absenceDates->absence_dates_array) * $absences->pluck('absenceHours')->flatten()->pluck('hours')->sum() : 0;
        }

        return $employee_absence_hours_used;
    }

    /*
         $absence_type =>  [1 => Holiday, 2 -> Leave], 
         $status => [1 => pending, 2 => approved, 3 => Rejected, 4 => Cancelled, 5 => approved but requested for cancellation]
    */
    public function formatAbsenceDataForMobileOverview($absence, $absence_type, $employee_flow = false) 
    {
        $response = [
            'pending'   => [],
            'approved'  => [],
            'cancelled' => [],
            'rejected'  => [],
        ];
        
        $absence->each(function ($leave) use (&$response, $absence_type, $employee_flow) {

            if ($absence_type == config('absence.LEAVE')) {
                $absence_data =  [
                    'id'             => $leave->id,
                    'applied_date'   => $leave->applied_date,
                    'duration_type'  => $leave->duration_type,
                    'dates'          => $leave->absenceDates->dates,
                    'dates_type'     => $leave->absenceDates->dates_type, # [1 =>  multiple dates, 2 => from and to date]
                    'leave_code'     => $leave->absenceHours->flatten()->pluck('holidayCode.holiday_code_name')->filter()->implode(', '),
                    'absence_status' => $leave->absence_status,
                    'employee_name'  => $leave->employee->full_name,
                    'employee_id'    => $leave->employee->id,
                    'manager_id'     => $leave->manager ? $leave->manager->id : null,
                    'manager_name'   => $leave->manager ? $leave->manager->full_name : null,
                    'reason'         => $leave->reason,
                    'plan_timings'   => $leave->plan_timings,
                    'plan_ids'       => $leave->plan_ids,
                    'actions'        => $this->getAbsenceActions($leave->absence_type, $leave->absence_status, $employee_flow),
                ];
            } else {  # Holiday
                
                $holiday_code           = [];
                $holiday_code_morning   = [];
                $holiday_code_evening   = [];
                $multiple_holiday_codes = [];
                
                $duration_type = $leave->duration_type;
                $half_day = null;
                if ($duration_type == config('absence.FULL_DAYS')) {
                    $half_day = 0;
                } elseif ($duration_type == config('absence.FIRST_HALF')) {
                    $half_day = 1;
                } elseif ($duration_type == config('absence.SECOND_HALF')) {
                    $half_day = 2;
                } elseif ($duration_type == config('absence.FIRST_AND_SECOND_HALF')) {
                    $half_day = 3;
                } elseif ($duration_type == config('absence.MULTIPLE_DATES')) {
                    $half_day = 4;
                }
 
                
                foreach ($leave->absenceHours as $absence_hours) {

                    if ($absence_hours->duration_type == config('absence.FULL_DAYS') || $absence_hours->duration_type == config('absence.PLANNING_LEAVES') || $absence_hours->duration_type == config('absence.MULTIPLE_DATES')) {
                        $holiday_code = [
                            'id'   => $absence_hours->holiday_code_id,
                            'name' => $absence_hours->holidayCode->holiday_code_name,
                            'hours' => $absence_hours->hours,
                        ];
                    } 

                    if ($absence_hours->duration_type == config('absence.FIRST_HALF')) {
                        $holiday_code_morning = [
                            'id'   => $absence_hours->holiday_code_id,
                            'name' => $absence_hours->holidayCode->holiday_code_name,
                            'hours' => $absence_hours->hours,
                        ];
                    } 
                    
                    if ($absence_hours->duration_type == config('absence.SECOND_HALF')) {
                        $holiday_code_evening = [
                            'id'   => $absence_hours->holiday_code_id,
                            'name' => $absence_hours->holidayCode->holiday_code_name,
                            'hours' => $absence_hours->hours,
                        ];
                    }  

                    if ($absence_hours->duration_type == config('absence.MULTIPLE_HOLIDAY_CODES_FIRST_HALF') || $absence_hours->duration_type == config('absence.MULTIPLE_HOLIDAY_CODES_SECOND_HALF') || $absence_hours->duration_type == config('absence.MULTIPLE_HOLIDAY_CODES')) {
                        $multiple_holiday_codes[] = [
                            'id'   => $absence_hours->holiday_code_id,
                            'name' => $absence_hours->holidayCode->holiday_code_name,
                            'hours' => $absence_hours->hours,
                        ];
                    }  
                }

                $absence_data = [
                    'id' => $leave->id,
                    'applied_date'                 => $leave->applied_date,
                    'duration_type'                => $duration_type,
                    'half_day'                     => $half_day,
                    'multiple_holiday_code_status' => in_array($duration_type, [config('absence.MULTIPLE_HOLIDAY_CODES'), config('absence.MULTIPLE_HOLIDAY_CODES_SECOND_HALF'), config('absence.MULTIPLE_HOLIDAY_CODES_FIRST_HALF')]),
                    'dates'                        => $leave->absenceDates->dates,
                    'dates_type'                   => $leave->absenceDates->dates_type, # [1 =>  multiple dates, 2 => from and to date]
                    'reason'                       => $leave->reason,
                    'plan_timings'                 => $leave->plan_timings,
                    'plan_ids'                     => $leave->plan_ids,
                    'shift_leave'                  => !empty($leave->plan_ids),
                    'employee_name'                => $leave->employee->full_name,
                    'employee_id'                  => $leave->employee->id,
                    'manager_id'                   => $leave->manager ? $leave->manager->id : null,
                    'manager_name'                 => $leave->manager ? $leave->manager->full_name : null,
                    'reason'                       => $leave->reason,
                    'actions'                      => $this->getAbsenceActions($leave->absence_type, $leave->absence_status, $employee_flow),
                    'holiday_code'                 => $holiday_code,
                    'holiday_code_morning'         => $holiday_code_morning,
                    'holiday_code_evening'         => $holiday_code_evening,
                    'multiple_holiday_codes'       => $multiple_holiday_codes,
                ];
            }
            

            if ($leave->absence_status == config('absence.PENDING') || $leave->absence_status == config('absence.REQUEST_CANCEL')) {
                $response['pending'][] = $absence_data;
            } elseif ($leave->absence_status == config('absence.APPROVE')) {
                $response['approved'][] = $absence_data;
            } elseif ($leave->absence_status == config('absence.REJECT')) {
                $response['rejected'][] = $absence_data;
            } elseif ($leave->absence_status == config('absence.CANCEL')) {
                $response['cancelled'][] = $absence_data;
            }
        }); 

        return $response;
    }

    public function getAbsenceActions($absence_type, $status, $employee_flow = false) # $absence_type =>  [1 => Holiday, 2 -> Leave]
    {
        $actions = ['approve' => false, 'reject' => false, 'change_manager' => false, 'request_cancel' => false, 'cancel' => false];
        if ($absence_type == config('absence.LEAVE')) {
            if ($status == config('absence.PENDING')) {
                $actions = $actions['approve'] = $actions['reject'] = true;
            } elseif ($status == config('absence.APPROVE')) {
                $actions['cancel'] = true;
            }
        } elseif ($absence_type == config('absence.HOLIDAY')) {    
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
        }

        return $actions;
    }

    public function formatDurationTypeForApplyingAbsence($half_day, $multiple_holiday_codes) # $half_day [0-> full day, 1-> morning, 2-> evening, 3-> both ]
    {
        if ($half_day == 0 && !$multiple_holiday_codes) {
            return config('absence.FULL_DAYS');
        } elseif ($half_day == 1 && $multiple_holiday_codes) {
            return config('absence.MULTIPLE_HOLIDAY_CODES_FIRST_HALF');
        } elseif ($half_day == 2 && $multiple_holiday_codes) {
            return config('absence.MULTIPLE_HOLIDAY_CODES_SECOND_HALF');
        } elseif ($multiple_holiday_codes) {
            return config('absence.MULTIPLE_HOLIDAY_CODES');
        } elseif ($half_day == 1) {
            return config('absence.FIRST_HALF');
        } elseif ($half_day == 2) {
            return config('absence.SECOND_HALF');
        } elseif ($half_day == 3) {
            return config('absence.FIRST_AND_SECOND_HALF');
        }
    }

    public function formatHolidayCodeCountsForApplyingAbsence($half_day, $multiple_holiday_codes, $holiday_code_counts, $holiday_code, $holiday_code_first_half, $holiday_code_second_half)
    {
        $duration_type = $this->formatDurationTypeForApplyingAbsence($half_day, $multiple_holiday_codes);

        $return_data = empty($holiday_code_counts) ? collect([]) : collect($holiday_code_counts);

        $return_data->transform(function ($holiday_count_details) {
            $holiday_count_details['duration_type'] = '';
            return $holiday_count_details;
        });

        if (in_array($duration_type, [
            config('absence.MULTIPLE_HOLIDAY_CODES_FIRST_HALF'), 
            config('absence.MULTIPLE_HOLIDAY_CODES_SECOND_HALF'), 
            config('absence.FIRST_HALF'), 
            config('absence.SECOND_HALF'), 
            config('absence.FULL_DAYS'), 
            ])) {

            $h_code = $d_type = '';
            if ($duration_type == config('absence.MULTIPLE_HOLIDAY_CODES_FIRST_HALF') || $duration_type == config('absence.FIRST_HALF')) {
                $d_type = config('absence.FIRST_HALF');
                $h_code = $holiday_code_first_half;
            } 
            if ($duration_type == config('absence.MULTIPLE_HOLIDAY_CODES_SECOND_HALF') || $duration_type == config('absence.SECOND_HALF')) {
                $d_type = config('absence.SECOND_HALF');
                $h_code = $holiday_code_second_half;
            } 
            
            if ($duration_type == config('absence.FULL_DAY') || $duration_type == config('absence.FULL_DAY')) { # fill day
                $h_code = $holiday_code;
            }

            $return_data->push([
                'holiday_code'  => $h_code,
                'hours'         => '',
                'duration_type' => $d_type,
            ]);

        } elseif ($duration_type == config('absence.FIRST_AND_SECOND_HALF')) {
            
            $return_data->push(
                [
                    'holiday_code'  => $holiday_code_first_half,
                    'hours'         => '',
                    'duration_type' => config('absence.FIRST_HALF'),
                ],
                [
                    'holiday_code'  => $holiday_code_second_half,
                    'hours'         => '',
                    'duration_type' => config('absence.SECOND_HALF'),
                ]
            );
        }

        return $return_data->toArray();
    }
}
