<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Company\Company;
use App\Models\Company\Employee\EmployeeProfile;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class AvailabilityRemarks extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tenant';
    protected $table = 'availability_remarks';
    protected $fillable  = [
        'employee_id',
        'company_id',
        'dates',
        'remark',
        'type'
    ];

    public $timestamps = true;

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function employeeProfile()
    {
        return $this->belongsTo(EmployeeProfile::class);
    }

}
