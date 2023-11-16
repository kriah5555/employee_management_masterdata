<?php

namespace App\Models\Company\Contract;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;
use App\Models\Company\Location;
use App\Models\EmployeeType\EmployeeType;

class ContractConfiguration extends BaseModel
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $table = 'contract_configurations';

    protected $fillable = [
        'status',
        'employee_type_id',
        'location_id',
        'created_by',
        'updated_by',
    ];

    public $timestamps = true;

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function locations()
    {
        return $this->hasMany(Location::class, 'location_id');
    }

    public function employeeTypes()
    {
        return $this->hasMany(EmployeeType::class, 'employee_type_id');
    }
}
