<?php

namespace App\Models\Planning;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class TimeRegistration extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tenant';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'time_registration';

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
        'plan_id',
        'actual_start_time',
        'actual_end_time',
        'status',
        'start_reason_id',
        'stop_reason_id',
        'started_by',
        'ended_by'
    ];
    
    public function planningBase()
    {
        return $this->belongsTo(PlanningBase::class, 'plan_id');
    }

    public function overtime()
    {
        return $this->hasOne(Overtime::class, 'time_registration_id');
    }
}
