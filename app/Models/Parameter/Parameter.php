<?php

namespace App\Models\Parameter;

use App\Models\BaseModel;
use App\Traits\UserAudit;

class Parameter extends BaseModel
{
    use UserAudit;

    const EMPLOYEE_TYPE_PARAMETER = 1;
    const SECTOR_PARAMETER = 2;
    const COMPANY_PARAMETER = 3;
    const EMPLOYEE_TYPE_SECTOR_PARAMETER = 4;
    const VALUE_TYPE_HOURS = 1;
    const VALUE_TYPE_MINUTES = 2;
    const VALUE_TYPE_DAYS = 3;
    const VALUE_TYPE_TIME = 4;
    const VALUE_TYPE_COUNT = 5;

    protected static $sort = ['type', 'name'];

    protected $columnsToLog = ['description', 'value'];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'parameters';

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
        'name',
        'description',
        'type',
        'value_type',
        'value',
        'status',
        'created_by',
        'updated_by'
    ];

    public function getParameterTypeName()
    {
        switch ($this->type) {
            case self::EMPLOYEE_TYPE_PARAMETER:
                $parameter_type = 'Employee type parameter';
                break;
            case self::SECTOR_PARAMETER:
                $parameter_type = 'Sector type';
                break;
            case self::COMPANY_PARAMETER:
                $parameter_type = 'Company parameter';
                break;
            case self::EMPLOYEE_TYPE_SECTOR_PARAMETER:
                $parameter_type = 'Employee type to sector parameter';
                break;
            default:
                break;
        }
        $this->parameter_type = $parameter_type;
        // return $parameter_type;
    }
}
