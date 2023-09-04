<?php

namespace App\Models\EmployeeType;

use App\Models\EmployeeType\EmployeeTypeCategory;
use App\Models\Contract\ContractType;
use App\Models\BaseModel;
use App\Models\Dimona\DimonaType;
use App\Models\EmployeeType\EmployeeTypeConfig;
use App\Models\EmployeeType\EmployeeTypeDimonaConfig;

class EmployeeType extends BaseModel
{
    protected static $sort = ['name'];
    protected $columnsToLog = ['name', 'description', 'employee_type_category_id', 'status'];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_types';

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
        "employee_type_category_id",
        'status',
        'created_by',
        'updated_by',
    ];

    public function employeeTypeCategory()
    {
        return $this->belongsTo(EmployeeTypeCategory::class);
    }
    public function employeeTypeCategoryValue()
    {
        return $this->belongsTo(EmployeeTypeCategory::class, 'employee_type_category_id')
            ->select('id as value', 'name as label')
            ->where('status', true);
    }

    public function contractTypes()
    {
        return $this->belongsToMany(ContractType::class, 'contract_type_employee_type');
    }

    public function contractTypesValue()
    {
        return $this->belongsToMany(ContractType::class, 'contract_type_employee_type')->select(['contract_type_id as value', 'name as label']);
    }
    public function dimonaTypeConfig()
    {
        return $this->hasOne(EmployeeTypeDimonaConfig::class);
    }

    public function employeeTypeConfig()
    {
        return $this->hasOne(EmployeeTypeConfig::class);
    }
}