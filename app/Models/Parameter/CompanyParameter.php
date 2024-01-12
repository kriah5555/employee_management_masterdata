<?php

namespace App\Models\Parameter;

use App\Models\BaseModel;
use App\Traits\UserAudit;
use App\Models\Parameter\EmployeeTypeParameter;
use App\Models\Parameter\SectorParameter;
use App\Models\Parameter\EmployeeTypeSectorParameter;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class CompanyParameter extends BaseModel
{
    use UserAudit;

    protected $columnsToLog = ['value'];
    protected $connection = 'tenant';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'company_parameters';

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

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
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
        'parameter_id',
        'parameter_type',
        'value',
        'created_by',
        'updated_by'
    ];
    public function employeeTypeParameter(): MorphToMany
    {
        return $this->morphedByMany(EmployeeTypeParameter::class, 'employee_type_parameters');
    }

    public function sectorParameter(): MorphToMany
    {
        return $this->morphedByMany(SectorParameter::class, 'parameter_type');
    }

    public function employeeTypeSectorParameter(): MorphToMany
    {
        return $this->morphedByMany(EmployeeTypeSectorParameter::class, 'parameter_type');
    }
}
