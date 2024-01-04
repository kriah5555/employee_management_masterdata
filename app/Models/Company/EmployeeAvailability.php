<?php

namespace App\Models\Company;

use App\Models\BaseModel;
use App\Models\Company\Employee\EmployeeProfile;

class EmployeeAvailability extends BaseModel
{
    protected static $sort = ['date'];
    protected $connection = 'tenant';
    protected $table = 'employee_availabilities';

    protected $fillable = [
        'employee_profile_id',
        'date',
        'availability'
    ];

    public $timestamps = true;

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function employeeProfile()
    {
        return $this->belongsTo(EmployeeProfile::class);
    }


    public function employeeAvailabilityRemarks()
    {
        return $this->hasOne(EmployeeAvailabilityRemarks::class);
    }
}
