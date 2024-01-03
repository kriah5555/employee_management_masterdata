<?php

namespace App\Models\Planning;

use App\Models\Company\Employee\EmployeeProfile;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;


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

    protected $appends = ['worked_hours'];

    public function getWorkedHoursAttribute()
    {
        if ($this->actual_end_time && $this->status) {
            $start = Carbon::parse($this->actual_start_time);
            $end   = Carbon::parse($this->actual_end_time);

            return $start->diffInHours($end);
        }

        return 0;
    }

    public function planningBase()
    {
        return $this->belongsTo(PlanningBase::class, 'plan_id');
    }

    public function overtime()
    {
        return $this->hasOne(Overtime::class, 'time_registration_id');
    }

    public function startedBy()
    {
        return $this->belongsTo(User::class, 'started_by');
    }

    public function endedBy()
    {
        return $this->belongsTo(User::class, 'ended_by');
    }
}
