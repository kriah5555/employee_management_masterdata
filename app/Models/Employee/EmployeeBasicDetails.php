<?php

namespace App\Models\Employee;

use App\Models\BaseModel;
use App\Traits\UserAudit;

class EmployeeBasicDetails extends BaseModel
{
    use UserAudit;

    protected $columnsToLog = [
        'employee_profile_id',
        'first_name',
        'last_name',
        'social_security_number',
        'gender_id',
        'date_of_birth',
        'place_of_birth',
        'license_expiry_date',
        'language',
        'extra_info',
        'status',
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_basic_details';

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
        'date_of_birth',
        'license_expiry_date',
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
        'first_name',
        'last_name',
        'social_security_number',
        'gender_id',
        'date_of_birth',
        'place_of_birth',
        'license_expiry_date',
        'language',
        'extra_info',
        'status',
    ];
}