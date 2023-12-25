<?php

namespace App\Models\Planning;

use App\Models\Company\{Workstation, Location};
use App\Models\BaseModel;
use App\Traits\UserAudit;


class PlanningShifts extends BaseModel
{
    use UserAudit;

    protected $connection = 'tenant';
    protected static $sort = ['start_time', 'end_time', 'contract_hours'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'planning_shifts';

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
        'start_time',
        'end_time',
        'contract_hours',
        'location_id',
        'workstation_id',
        'status',
        'created_by',
        'updated_by'
    ];
    protected $columnsToLog = [
        'start_time',
        'end_time',
        'contract_hours',
        'location_id',
        'workstation_id',
        'status',
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
    public function workstation()
    {
        return $this->belongsTo(Workstation::class, 'workstation_id');
    }
}
