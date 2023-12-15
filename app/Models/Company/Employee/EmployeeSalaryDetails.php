<?php

namespace App\Models\Company\Employee;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Company\Employee\EmployeeFunctionDetails;

class EmployeeSalaryDetails extends BaseModel
{
    protected $connection = 'tenant';

    use HasFactory;


    protected $table = 'employee_salary_details';

    protected $fillable = [
        'employee_profile_id',
        'salary',
        'status'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $appends = ['salary_european'];

    public function setSalaryAttribute($value)
    {
        $this->attributes['salary'] = formatToNumber($value);
    }

    public function getSalaryEuropeanAttribute()
    {
        return formatToEuropeCurrency($this->salary);
    }

    public function employeeFunctionDetails()
    {
        return $this->hasOne(EmployeeFunctionDetails::class);
    }

}
