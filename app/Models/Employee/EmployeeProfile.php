<?php

namespace App\Models\Employee;

use App\Models\BaseModel;
use App\Traits\UserAudit;

class EmployeeProfile extends BaseModel
{
    use UserAudit;
    protected static $sort = ['first_name', 'last_name'];
    protected $columnsToLog = [
        'uid',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'email',
        'phone_number',
        'social_security_number',
        'place_of_birth',
        'date_of_joining',
        'date_of_leaving',
        'language',
        'marital_status',
        'dependent_spouse',
        'bank_account_id',
        'address_id',
        'company_id',
        'status',
        'extra_info'
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_profiles';

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
        'date_of_joining',
        'date_of_leaving',
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
        'uid',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'email',
        'phone_number',
        'social_security_number',
        'place_of_birth',
        'license_expiry_date',
        'language',
        'marital_status',
        'dependent_spouse',
        'bank_account_id',
        'address_id',
        'company_id',
        'status',
        'extra_info'
    ];
}
