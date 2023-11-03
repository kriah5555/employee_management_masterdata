<?php

namespace App\Models\Contract;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;
use App\Models\Company\Company;
use App\Models\Sector\Sector;
use App\Models\EmployeeType\EmployeeType;
use App\Models\SocialSecretary\SocialSecretary;

class ContractTemplate extends BaseModel
{
    use HasFactory;
    
    protected $connection = 'master';

    protected $table = 'contract_templates';

    protected $primaryKey = 'id';
    
    protected $fillable = [
        'body',
        'language',
        'status',
        'employee_type_id',
        'social_secretary_id',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function employeeType()
    {
        return $this->belongsTo(EmployeeType::class, 'employee_type_id');
    }

    public function socialSecretary()
    {
        return $this->belongsTo(SocialSecretary::class, 'social_secretary_id');
    }
}
