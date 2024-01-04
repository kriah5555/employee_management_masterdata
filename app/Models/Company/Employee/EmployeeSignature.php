<?php

namespace App\Models\Company\Employee;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Company\Employee\EmployeeProfile;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class EmployeeSignature extends BaseModel
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $fillable = [
        'employee_profile_id',
        'signature_data',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function employeeProfile()
    {
        return $this->belongsTo(EmployeeProfile::class, 'employee_profile_id');
    }
}
