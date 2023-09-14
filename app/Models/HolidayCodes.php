<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Company;

class HolidayCodes extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'holiday_codes';

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

        static::created(function ($holidayCode) {
        $companies = Company::all();

        // Attach the newly created holiday code to all companies
        $companies->each(function ($company) use ($holidayCode) {
                $company->holidayCodes()->attach($holidayCode);
            });
        });
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
}
