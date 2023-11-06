<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;
use App\Models\Company\Company;
use App\Models\Sector\Sector;
use App\Models\EmployeeType\EmployeeType;
use App\Models\SocialSecretary\SocialSecretary;
use App\Models\Contract\ContractTemplate;

class CompanyContractTemplate extends BaseModel
{
    use HasFactory;
    
    protected $connection = 'tenant';

    protected $table = 'contract_templates';

    protected $primaryKey = 'id';
    
    protected $fillable = [
        'body',
        'language',
        'status',
        'employee_type_id'
    ];

    public function employeeType()
    {
        return $this->belongsTo(EmployeeType::class, 'employee_type_id');
    }
}
