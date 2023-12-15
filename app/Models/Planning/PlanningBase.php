<?php

namespace App\Models\Planning;

use App\Models\EmployeeType\EmployeeType;
use Illuminate\Support\Facades\DB;
use App\Models\Company\{Workstation, Location};
use App\Models\Company\Employee\EmployeeProfile;
use App\Models\Planning\{PlanningBreak, PlanningContracts, PlanningDimona, TimeRegistration};
use App\Models\BaseModel;
use App\Traits\UserAudit;


class PlanningBase extends BaseModel
{
    use UserAudit;

    protected $connection = 'tenant';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'planning_base';

    // protected static $sort = ['start_date_time'];
    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'location_id',
        'workstation_id',
        'function_id',
        'start_date_time',
        'end_date_time',
        'contract_hours',
        'employee_type_id',
        'plan_type',
        'employee_profile_id',
        'mail_status',
        'contract_status',
        'dimona_status',
        'status',
        'created_by',
        'updated_by'
    ];

    /**
     * Adding the inverse relation with Location models
     *
     * @return void
     */
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
    public function employeeType()
    {
        return $this->belongsTo(EmployeeType::class, 'employee_type_id');
    }

    /**
     * Adding the inverse relation with workstation models
     *
     * @return void
     */
    public function workStation()
    {
        return $this->belongsTo(Workstation::class, 'workstation_id');
    }

    /**
     * Adding the inverse relation with employee profile models
     *
     * @return void
     */
    public function employeeProfile()
    {
        return $this->belongsTo(EmployeeProfile::class, 'employee_profile_id');
    }


    /**
     * Getting multiple records from one-to-many relationship with timeregistraions Model.
     *
     * @return void
     */
    public function timeRegistrations()
    {
        return $this->hasMany(TimeRegistration::class, 'plan_id');
    }

    /**
     * Getting multiple records from one-to-many relationship with contract Model.
     *
     * @return void
     */
    public function contracts()
    {
        return $this->hasMany(PlanningContracts::class, 'plan_id');
    }

    /**
     * Getting multiple records from one-to-many relationship with breaks Model.
     *
     * @return void
     */
    public function breaks()
    {
        return $this->hasMany(PlanningBreak::class, 'plan_id');
    }

    /**
     * Get planning for the monthly overview.
     *
     * @return array
     */
    public function monthPlanning($year, $month, $locations, $workstations, $employee_types)
    {
        $monthDates = getStartAndEndDateOfMonth($month, $year);
        $query = $this->select(DB::raw('count(*) as count'), DB::raw('start_date_time::date as date'))
            ->whereNull('deleted_at')
            ->groupBy(DB::raw('start_date_time::date'));

        if (!empty($year)) {
            $query->whereYear('start_date_time', '=', $year);
        }

        if (!empty($locations)) {
            $query->whereIn('location_id', (array) $locations);
        }

        if (!empty($workstations)) {
            $query->whereIn('workstation_id', (array) $workstations);
        }

        if (!empty($employee_types)) {
            $query->whereIn('employee_type_id', $employee_types);
        }
        return $query->get()->toArray();
    }

    /**
     * Planing weekly overview query.
     * @todo: furher modification are required.
     * @param  [type] $locations
     * @param  [type] $workstations
     * @param  [type] $employee_types
     * @param  [type] $weekNo
     * @param  [type] $year
     * @return array
     */
    public function weeklyPlanning($locations, $workstations, $employee_types, $weekNo, $year)
    {
        $query = $this->select(
            [
                'planning_base.*', 'w.*', 'l.*', 'ep.*',
                DB::raw("EXTRACT('week' FROM start_date_time) as week_number"),
                DB::raw("start_date_time::date as start_date"),
                DB::raw("start_date_time::time as start_time"),
                DB::raw("end_date_time::date as end_date"),
                DB::raw("end_date_time::time as end_time"),
            ]
        )
            ->join('workstations as w', 'w.id', '=', 'planning_base.workstation_id')
            ->join('locations as l', 'l.id', '=', 'planning_base.location_id')
            ->join('employee_profiles as ep', 'ep.id', '=', 'planning_base.employee_profile_id');

        if (!empty($year)) {
            $query->whereYear('start_date_time', '=', $year);
        }

        if (!empty($locations)) {
            $query->whereIn('location_id', (array) $locations);
        }

        if (!empty($workstations)) {
            $query->whereIn('workstation_id', (array) $workstations);
        }

        if (!empty($employee_types)) {
            $query->whereIn('employee_type_id', $employee_types);
        }

        if (!empty($weekNo)) {
            $query->whereRaw("EXTRACT('week' FROM start_date_time) = ?", [$weekNo]);
        }
        return $query->get()->toArray();
    }

    /**
     * Day planning query.
     *
     * @param  [type] $locations
     * @param  [type] $workstations
     * @param  [type] $employee_types
     * @param  [type] $date
     * @return object
     */
    public function dayPlanning($locations, $workstations, $employee_types, $date): mixed
    {
        // Assuming you have an instance of PlanningBase model
        $query = $this->whereDate('start_date_time', $date);

        if (!empty($employee_types)) {
            $query->whereIn('employee_type_id', (array) $employee_types);
        }

        if (!empty($locations)) {
            $query->whereIn('location_id', (array) $locations);
        }

        if (!empty($workstations)) {
            $query->whereIn('workstation_id', (array) $workstations);
        }

        return $query->get();
    }
}
