<?php

namespace App\Models\Employee;

use App\Models\BaseModel;
use App\Traits\UserAudit;

class EmployeeAddress extends BaseModel
{
    use UserAudit;

    protected $columnsToLog = [
        'employee_profile_id',
        'account_number',
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_bank_account_details';

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
        'account_number',
    ];
}