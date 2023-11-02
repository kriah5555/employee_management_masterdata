<?php

namespace App\Models\Employee;

use App\Models\BaseModel;
use App\Models\EmployeeType\EmployeeType;
use App\Traits\UserAudit;

class EmployeeContractDetails extends BaseModel
{
    use UserAudit;
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
    protected $table = 'employee_contract_details';

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
        'from_date',
        'to_date'
    ];


    public function employeeType()
    {
        return $this->belongsTo(EmployeeType::class);
    }
}
