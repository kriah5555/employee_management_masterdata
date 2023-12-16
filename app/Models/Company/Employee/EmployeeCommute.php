<?php

namespace App\Models\Company\Employee;

use App\Models\BaseModel;
use App\Traits\UserAudit;
use App\Models\Company\Employee\CommuteType;
use App\Models\Company\Employee\EmployeeProfile;
use App\Models\Company\Location;

class EmployeeCommute extends BaseModel
{
    use UserAudit;

    protected $connection = 'tenant';

    protected $columnsToLog = [
        'employee_profile_id',
        'commute_type_id',
        'location_id',
        'distance',
        'status',
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_commute';

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
        'commute_type_id',
        'location_id',
        'distance',
        'status',
    ];

    
    public function employeeProfile()
    {
        return $this->belongsTo(EmployeeProfile::class, 'employee_profile_id');
    }


    public function commuteType()
    {
        return $this->belongsTo(CommuteType::class, 'commute_type_id');
    }

    function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}