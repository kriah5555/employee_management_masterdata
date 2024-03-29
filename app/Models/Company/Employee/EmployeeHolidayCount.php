<?php

namespace App\Models\Company\Employee;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Holiday\HolidayCodes;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Holiday\EmployeeHolidayCountReasons;

class EmployeeHolidayCount extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tenant';

    protected $casts = [
        'count' => 'float', // Cast the 'count' attribute to a float
    ];


    protected $table = 'employee_holiday_count';

    protected $fillable = [
        'employee_id',
        'holiday_code_id',
        'count',
        'status',
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    function holidayCode()
    {
        return $this->hasMany(HolidayCode::class, 'holiday_code_id');
    }

    public function reasons()
    {
        return $this->hasMany(EmployeeHolidayCountReasons::class, 'employee_holiday_count_id');
    }
}