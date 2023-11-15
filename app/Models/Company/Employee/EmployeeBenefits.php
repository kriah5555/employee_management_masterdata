<?php

namespace App\Models\Company\Employee;

use App\Models\BaseModel;
use App\Traits\UserAudit;

class EmployeeBenefits extends BaseModel
{
    use UserAudit;

    protected $connection = 'tenant';

    protected $table = 'employee_benefits';

    protected $columnsToLog = [
        'employee_profile_id',
        'fuel_card',
        'company_car',
        'clothing_compensation',
        'clothing_size',
        'status',
    ];

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
        'fuel_card',
        'company_car',
        'clothing_compensation',
        'clothing_size',
        'status',
    ];
}