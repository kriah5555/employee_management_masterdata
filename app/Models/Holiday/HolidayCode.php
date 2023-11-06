<?php

namespace App\Models\Holiday;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\EmployeeType\EmployeeType;
use App\Models\Company\Company;

class HolidayCode extends Model
{
    protected $connection = 'master';

    use HasFactory, SoftDeletes;

    protected $table = 'holiday_codes';

    protected $casts = [
        'count' => 'float', // Cast the 'count' attribute to a float
    ];

    protected $fillable = [
        'holiday_code_name',
        'count',
        'internal_code',
        'description',
        'holiday_type',
        'count_type',
        'icon_type',
        'consider_plan_hours_in_week_hours',
        'employee_category',
        'contract_type',
        'status'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected static function boot()
    {
        parent::boot();

        self::creating(function ($holidayCode) {
            $holidayCode->processCountAttribute();
        });

        self::updating(function ($holidayCode) {
            $holidayCode->processCountAttribute();
        });

        // static::created(function ($holidayCode) {
        //     $companies = Company::all();

        //     // Attach the newly created holiday code to all companies
        //     $companies->each(function ($company) use ($holidayCode) {
        //             $company->holidayCodes()->attach($holidayCode);
        //     });
        // });
    }

    private function processCountAttribute()
    {
        if ($this->count_type == 2) {
            // If count_type is 2 => Days, multiply count by 8
            $this->count = $this->count * config('constants.DAY_HOURS');
        }
    }

    # Link holiday codes to companies by externally calling this function
    public function linkToCompanies()
    {
        $companies = Company::all();

        // Attach this holiday code to all companies
        $companies->each(function ($company) {
            $company->holidayCodes()->attach($this->id);
        });
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_holiday_codes', 'holiday_code_id', 'company_id');
    }

    public function employeeTypes()
    {
        return $this->belongsToMany(EmployeeType::class, 'employee_type_holiday_codes', 'holiday_code_id', 'employee_type_id');
    }

    public function employeeTypesValue()
    {
        return $this->belongsToMany(EmployeeType::class, 'employee_type_holiday_codes', 'holiday_code_id', 'employee_type_id')->select(['employee_type_id as value', 'name as label']);
    }

    # link the holiday codes with company
    public function linkCompanies($link, $company_ids)
    {
        $companies = Company::query();

        if ($link === 'include') {
            $companies->whereIn('id', $company_ids);
        } elseif ($link === 'exclude') {
            $companies->whereNotIn('id', $company_ids);
        }

        $companies->get()->each(function ($company) {
            $company->holidayCodes()->syncWithoutDetaching($this->id);
        });
    }
}