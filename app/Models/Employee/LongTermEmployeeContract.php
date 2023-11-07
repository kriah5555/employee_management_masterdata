<?php

namespace App\Models\Employee;

use App\Models\BaseModel;
use App\Traits\UserAudit;

class LongTermEmployeeContract extends BaseModel
{
    use UserAudit;

    protected $connection = 'tenant';

    protected $columnsToLog = [
        'employee_contract_id',
        'sub_type',
        'schedule_type',
        'employement_type',
        'weekly_contract_hours',
        'work_days_per_week',
        'status'
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'long_term_employee_contract';

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
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_contract_id',
        'sub_type',
        'schedule_type',
        'employement_type',
        'weekly_contract_hours',
        'work_days_per_week',
        'status'
    ];
}
