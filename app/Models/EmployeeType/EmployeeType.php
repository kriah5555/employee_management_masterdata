<?php

namespace App\Models\EmployeeType;

use App\Models\EmployeeType\EmployeeTypeCategory;
use App\Models\Contract\ContractType;
use App\Models\BaseModel;
use App\Models\EmployeeType\EmployeeTypeConfig;
use App\Models\EmployeeType\EmployeeTypeDimonaConfig;
use App\Traits\UserAudit;
use App\Models\Sector\Sector;

class EmployeeType extends BaseModel
{
    use UserAudit;
    
    protected $connection = 'master';
    
    protected $table = 'employee_types';

    protected static $sort = ['name'];
    
    protected $columnsToLog = ['name', 'description', 'employee_type_category_id', 'status', 'salary_type'];
    /**
     * The table associated with the model.
     *
     * @var string
     */

    protected $hidden = ['pivot'];

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;


    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'employee_type_category_id',
        'salary_type',
        'status',
        'created_by',
        'updated_by',
        'dimona_code',
    ];

    public function employeeTypeCategory()
    {
        return $this->belongsTo(EmployeeTypeCategory::class);
    }

    public function contractTypes()
    {
        return $this->belongsToMany(ContractType::class, 'contract_type_employee_type');
    }

    public function employeeTypeConfig()
    {
        return $this->hasOne(EmployeeTypeConfig::class);
    }

    public function dimonaConfig()
    {
        return $this->hasOne(EmployeeTypeDimonaConfig::class);
    }

    public function sectors()
    {
        return $this->belongsToMany(Sector::class, 'sector_to_employee_types');
    }
}
