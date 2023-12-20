<?php

namespace App\Models\Company\Employee;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}