<?php

namespace App\Models\Holiday;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\EmployeeType\EmployeeType;
use App\Models\Company\Company;
use App\Models\BaseModel;

class HolidayCode extends BaseModel
{
    protected $connection = 'master';

    protected static $sort = ['holiday_code_name', 'internal_code'];

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
        'holiday_type', # [1 => 'Paid', 2 => 'Unpaid', 3 => 'Sick Leave']
        'count_type', # [1 => 'Hours', 2 => 'Days', 3 => 'Sick Leave']
        'icon_type', # [1 => 'Illness', 2 => 'Holiday', 3 => 'Unemployed', 4 => 'Others']
        'consider_plan_hours_in_week_hours', # [0 => 'No', 1 => 'Yes']
        'employee_category', #  [1 => 'HQ servant', 2 => 'Servant', 3 => 'Worker']
        'contract_type', # [1 => 'Both', 2 => 'Full time', 3 => 'Part time']
        'status',
        'type', # [1 => 'Holiday', 2 => 'Leave']
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($holidayCode) {
            // $holidayCode->processCountAttribute();
            $holidayCode->count_type = 1;
        });

        // self::updating(function ($holidayCode) {
        //     $holidayCode->processCountAttribute();
        // });

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