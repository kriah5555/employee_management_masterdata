<?php

namespace App\Models\Company\Employee;

use App\Models\BaseModel;
use App\Models\EmployeeFunction\FunctionTitle;
use App\Traits\UserAudit;
use App\Models\Company\Employee\EmployeeSalaryDetails;
class EmployeeFunctionDetails extends BaseModel
{
    use UserAudit;

    protected $connection = 'tenant';

    protected $columnsToLog = [
        'employee_contract_id',
        'function_id',
        'salary_id'
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_function_details';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    
    public $timestamps = true;


    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'employee_contract_id',
        'experience',
        'function_id',
        'salary_id'
    ];
    
    public function employeeContract()
    {
        return $this->belongsTo(EmployeeContract::class);
    }

    public function functionTitle()
    {
        return $this->belongsTo(FunctionTitle::class, 'function_id');
    }

    public function salary()
    {
        return $this->belongsTo(EmployeeSalaryDetails::class, 'salary_id');
    }
}
