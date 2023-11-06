<?php

namespace App\Models\EmployeeType;

use App\Models\EmployeeType\EmployeeType;
use App\Models\Contract\ContractType;
use App\Models\BaseModel;
use App\Models\Dimona\DimonaType;

class EmployeeTypeConfig extends BaseModel
{
    protected $connection = 'master';

    protected static $sort = [];

    protected $columnsToLog = [
        'employee_type_id',
        'consecutive_days_limit',
        'icon_color',
        'start_in_past',
        'counters',
        'contract_hours_split',
        'leave_access',
        'holiday_access',
        'status'
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_type_config';

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
        'employee_type_id',
        'consecutive_days_limit',
        'icon_color',
        'start_in_past',
        'counters',
        'contract_hours_split',
        'leave_access',
        'holiday_access',
        'created_by',
        'updated_by'
    ];

    public function employeeType()
    {
        return $this->hasOne(EmployeeType::class);
    }
}
