<?php

namespace App\Models\Employee;

use App\Models\BaseModel;
use App\Traits\UserAudit;

class EmployeeBenefits extends BaseModel
{
    use UserAudit;

    protected $columnsToLog = [
        'employee_profile_id',
        'fuel_card',
        'company_car',
        'clothing_compensation',
        'clothing_size',
        'status',
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_benefits';

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