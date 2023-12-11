<?php

namespace App\Models\Planning;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class EventDepartmentDetails extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tenant';

    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'event_department_details';

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
        'event_details_id',
        'department_id',
        'function_id',
        'start_time',
        'end_time',
        'extra_info',
        'employee_count',
        'created_by',
        'updated_by'
    ];

    public function eventDetails()
    {
        return $this->belongsTo(EventDetails::class, 'event_details_id');
    }
}
