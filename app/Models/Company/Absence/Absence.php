<?php

namespace App\Models\Company\Absence;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Company\Employee\EmployeeProfile;
use App\Models\Company\Absence\AbsenceDates;

class Absence extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tenant';

    protected $table = 'absence';

    protected $fillable = [
        'shift_type', # [1 => Holiday, 2 -> Leave]
        'duration_type', #  [1 => first half, 2 => second half, 3 => full day, 4 => multiple codes or combination]
        'absence_status', # [1 => pending, 2 => approved, 3 => Rejected, 4 => Cancelled]
        'employee_profile_id',
        'manager_id',
        'reason',
        'status',
    ];
    
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function employee()
    {
        return $this->hasOne(EmployeeProfile::class, 'employee_profile_id');
    }

    public function manager()
    {
        return $this->hasOne(EmployeeProfile::class, 'manager_id');
    }

    public function absenceDates()
    {
        return $this->belongTo(AbsenceDates::class);
    }
}
