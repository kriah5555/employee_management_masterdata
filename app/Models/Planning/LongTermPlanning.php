<?php

namespace App\Models\Planning;

use App\Models\BaseModel;
use App\Models\Company\Workstation;
use App\Models\EmployeeFunction\FunctionTitle;
use App\Traits\UserAudit;
use App\Models\Company\Location;


class LongTermPlanning extends BaseModel
{
    use UserAudit;

    protected $connection = 'tenant';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'long_term_planning';

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
        'employee_profile_id',
        'function_id',
        'workstation_id',
        'location_id',
        'start_date',
        'end_date',
        'repeating_week',
        'auto_renew'
    ];
    public function longTermPlanningTimings()
    {
        return $this->hasMany(LongTermPlanningTimings::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function workstation()
    {
        return $this->belongsTo(Workstation::class, 'workstation_id');
    }

    public function functionTitle()
    {
        return $this->belongsTo(FunctionTitle::class, 'function_id');
    }
}
