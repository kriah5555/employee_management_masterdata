<?php

namespace App\Models\Planning;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class PlanningBreak extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tenant';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'planning_break';

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
        'break_start_time',
        'break_end_time',
        'status',
        'started_by',
        'ended_by'
    ];

    public function planningBase()
    {
        return $this->belongsTo(PlanningBase::class, 'plan_id');
    }

    protected $appends = ['break_hours'];

    public function getBreakHoursAttribute()
    {
        if ($this->break_end_time && $this->status) {
            $start = Carbon::parse($this->break_start_time);
            $end   = Carbon::parse($this->break_end_time);

            $hours = $start->floatDiffInHours($end);
            return round($hours, 3);
        }
        
        return 0;
    }
}
