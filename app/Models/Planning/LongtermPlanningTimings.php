<?php

namespace App\Models\Planning;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class LongtermPlanningTimings extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tenant';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'longterm_planning_timings';

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
        'longterm_planning_id',
        'day',
        'start_time',
        'end_time',
        'contract_hours',
        'week_no',
        'status'
    ];
    
    public function longtermPlanning()
    {
        return $this->belongsTo(LongtermPlanning::class, 'longterm_planning_id');
    }
}
