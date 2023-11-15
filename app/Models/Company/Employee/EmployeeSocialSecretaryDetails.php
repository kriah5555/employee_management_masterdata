<?php

namespace App\Models\Company\Employee;

use App\Models\BaseModel;
use App\Traits\UserAudit;

class EmployeeSocialSecretaryDetails extends BaseModel
{
    use UserAudit;

    protected $connection = 'tenant';

    protected $columnsToLog = [
        'employee_profile_id',
        'social_secretary_number',
        'contract_number',
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_social_secretary_details';

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
        'social_secretary_number',
        'contract_number',
    ];
}