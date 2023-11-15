<?php

namespace App\Models\Holiday;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Company\Employee\EmployeeHolidayCount;

class EmployeeHolidayCountReasons extends Model
{
    protected $connection = 'tenant';

    use HasFactory, SoftDeletes;

    protected $table = 'employee_holiday_count_reasons';

    protected $casts = [
        'count' => 'float', // Cast the 'count' attribute to a float
    ];

    protected $fillable = [
        'employee_holiday_count_id',
        'count',
        'reason',
        'count_type',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Define the relationship to EmployeeHolidayCount
    public function employeeHolidayCount()
    {
        return $this->belongsTo(EmployeeHolidayCount::class, 'employee_holiday_count_id');
    }

}
