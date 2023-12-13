<?php

namespace App\Models\Company\Employee;

use App\Models\BaseModel;
use App\Models\EmployeeType\EmployeeType;
use App\Traits\UserAudit;
use App\Models\Company\Employee\LongTermEmployeeContract;

class EmployeeContract extends BaseModel
{
    use UserAudit;

    protected static $sort = ['start_date', 'end_date'];
    protected $connection = 'tenant';

    protected $columnsToLog = [
        'employee_profile_id',
        'employee_type_id',
        'start_date',
        'end_date'
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_contract';

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
        'deleted_at',
        'start_date',
        'end_date'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_profile_id',
        'employee_type_id',
        'start_date',
        'end_date'
    ];
    protected $apiValues = [
        'employee_profile_id',
        'employee_type_id',
        'start_date',
        'end_date'
    ];


    public function employeeType()
    {
        return $this->belongsTo(EmployeeType::class);
    }
    public function longTermEmployeeContract()
    {
        return $this->hasOne(LongTermEmployeeContract::class);
    }
    public function employeeProfile()
    {
        return $this->belongsTo(EmployeeProfile::class);
    }
}
