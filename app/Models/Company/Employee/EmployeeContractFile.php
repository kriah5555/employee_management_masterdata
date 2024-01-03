<?php

namespace App\Models\Company\Employee;

use Illuminate\Database\Eloquent\Model;
use App\Models\Company\Employee\EmployeeContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Planning\Files;
use App\Models\Planning\PlanningBase;

class EmployeeContractFile extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $table = 'employee_contract_files';

    protected $fillable = [
        'employee_profile_id',
        'employee_contract_id',
        'planning_base_id',
        'file_id',
        'contract_status', # [1 => unsigned, 2 => signed]
        'status',
    ];

    protected $appends = ['file_url'];

    public function employeeContract()
    {
        return $this->hasOne(EmployeeContract::class);
    }

    public function plan()
    {
        return $this->hasOne(PlanningBase::class, 'planning_base_id');
    }

    public function files()
    {
        return $this->belongsTo(Files::class, 'file_id');
    }


    public function getFileUrlAttribute()
    {
        exit;
        return env('CONTRACTS_URL') . $this->files->first()->file_path;
    }
}
