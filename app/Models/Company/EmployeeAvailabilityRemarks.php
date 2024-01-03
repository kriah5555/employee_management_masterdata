<?php

namespace App\Models\Company;

use App\Models\BaseModel;
use App\Models\Company\Company;
use App\Models\Company\Employee\EmployeeProfile;

class EmployeeAvailabilityRemarks extends BaseModel
{

    protected $connection = 'tenant';
    protected $table = 'employee_availability_remarks';
    protected $fillable = [
        'employee_availability_id',
        'remark',
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

}
