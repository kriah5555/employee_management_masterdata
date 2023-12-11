<?php

namespace App\Models\Planning;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Company\{Workstation, Location};
use App\Models\Employee\EmployeeProfile;
use Illuminate\Support\Facades\DB;


class PlanningBase extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tenant';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'planning_base';

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


    public function workstation()
    {
        return $this->hasOne(Workstation::class, 'id', 'workstation_id');
    }

    public function location()
    {
        return $this->hasOne(Location::class, 'id', 'location_id');
    }

    public function employeeProfile()
    {
        return $this->hasOne(EmployeeProfile::class, 'id', 'employee_profile_id');
    }


    public function monthPlanning($year, $locations, $workstations, $employee_types)
    {
        $query = $this->select(DB::raw('count(*) as count'), DB::raw('start_date_time::date as date'))
            ->whereNull('deleted_at')
            ->groupBy(DB::raw('start_date_time::date'));

            if (!empty($year)) {
                $query->whereYear('start_date_time', '=', $year);
            }

            if (!empty($locations)) {
                $query->whereIn('location_id', (array)$locations);
            }

            if (!empty($workstations)) {
                $query->whereIn('workstation_id', (array)$workstations);
            }

            if (!empty($employee_types)) {
                $query->whereIn('employee_type_id', $employee_types);
            }
            return $query->get()->toArray();
    }

    public function weeklyPlanning($locations, $workstations, $employee_types, $weekNo, $year)
    {
        $query =  $this->select(
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
            $query->whereIn('location_id', (array)$locations);
        }

        if (!empty($workstations)) {
            $query->whereIn('workstation_id', (array)$workstations);
        }

        if (!empty($employee_types)) {
            $query->whereIn('employee_type_id', $employee_types);
        }

        if (!empty($weekNo)) {
            $query->whereRaw("EXTRACT('week' FROM start_date_time) = ?", [$weekNo]);
        }
        return $query->get()->toArray();
    }
}
