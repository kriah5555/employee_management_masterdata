<?php

namespace App\Models\Company\Employee;

use App\Models\BaseModel;
use App\Traits\UserAudit;
use App\Models\Company\Employee\EmployeeProfile;
use App\Models\MealVoucher;
class EmployeeBenefits extends BaseModel
{
    use UserAudit;

    protected $connection = 'tenant';

    protected $table = 'employee_benefits';

    protected $columnsToLog = [
        'employee_profile_id',
        'fuel_card',
        'company_car',
        'meal_voucher_id',
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
        'meal_voucher_id',
        'status',
    ];

    protected $with = ['mealVoucher'];

    public function mealVoucher()
    {
        return $this->belongsTo(MealVoucher::class, 'meal_voucher_id');
    }

    public function employeeProfile()
    {
        return $this->belongsTo(EmployeeProfile::class, 'employee_profile_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($employeeBenefits) {
            if (!empty($employeeBenefits->clothing_compensation)) {
                $employeeBenefits->clothing_compensation = formatToNumber($employeeBenefits->clothing_compensation);
            }
        });
    }

    protected $appends = ['clothing_compensation_european'];

    public function getClothingCompensationEuropeanAttribute()
    {
        return formatToEuropeCurrency($this->clothing_compensation);
    }
}