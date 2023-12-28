<?php

namespace App\Models\Company\Employee;

use Illuminate\Database\Eloquent\Model;
use App\Models\Company\Employee\EmployeeContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Planning\Files;

class EmployeeContractFile extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $table = 'employee_contract_files';

    protected $fillable = [
        'employee_profile_id',
        'employee_contract_id',
        'file_id',
        'contract_status', # [1 => unsigned, 2 => signed]
        'status',
    ];

    public function employeeContract()
    {
        return $this->hasOne(EmployeeContract::class);
        // return $this->hasOne(EmployeeContract::class, 'employee_contract_id');
    }

    public function files()
    {
        return $this->belongsTo(Files::class, 'file_id');
    }
}
