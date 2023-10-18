<?php

namespace App\Models\Contract;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;
use App\Models\Company;
use App\Models\Sector\Sector;
use App\Models\EmployeeType\EmployeeType;
use App\Models\SocialSecretary\SocialSecretary;

class ContractTemplate extends BaseModel
{
    use HasFactory;

    protected $table = 'contract_templates';

    protected $primaryKey = 'id';
    
    protected $fillable = [
        'body',
        'language',
        'status',
        'employee_type_id',
        'social_secretary_id',
        'sector_id',
        'company_id',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function companyValue()
    {
        if ($this->company) {
            return [
                'level' => $this->company->id,
                'value' => $this->company->company_name, // Replace 'company_name' with the actual column name for the company name in your Company model
            ];
        } else {
            return null;
        }
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class, 'sector_id');
    }

    public function sectorValue()
    {
        if ($this->sector) {
            return [
                'level' => $this->sector->id,
                'value' => $this->sector->name, // Replace 'company_name' with the actual column name for the company name in your Company model
            ];
        } else {
            return null;
        }
    }

    public function employeeType()
    {
        return $this->belongsTo(EmployeeType::class, 'employee_type_id');
    }

    public function employeeTypeValue()
    {
        if ($this->employeeType) {
            return [
                'level' => $this->employeeType->id,
                'value' => $this->employeeType->name, // Replace 'company_name' with the actual column name for the company name in your Company model
            ];
        } else {
            return null;
        }
    }

    public function getEmployeeTypeValue()
    {
        return $this->belongsTo(EmployeeType::class, 'employee_type_id');
    }

    public function socialSecretary()
    {
        return $this->belongsTo(SocialSecretary::class, 'social_secretary_id');
    }

    public function socialSecretaryValue()
    {   
        if ($this->socialSecretary) {
            return [
                'level' => $this->socialSecretary->id,
                'value' => $this->socialSecretary->name, // Replace 'name' with the actual column name for the social secretary name in your SocialSecretary model
            ];
        } else {
            return null;
        }
    }
}
