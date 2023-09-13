<?php

namespace App\Models\Rule;

use App\Models\BaseModel;
use App\Traits\UserAudit;

class Rule extends BaseModel
{
    use UserAudit;

    const EMPLOYEE_TYPE_RULE = 1;
    const SECTOR_RULE = 2;
    const COMPANY_RULE = 3;
    const EMPLOYEE_TYPE_SECTOR_RULE = 4;
    const VALUE_TYPE_MINUTES = 1;
    const VALUE_TYPE_HOURS = 2;
    const VALUE_TYPE_DAYS = 3;
    const VALUE_TYPE_TIME = 4;
    const VALUE_TYPE_COUNT = 5;

    protected static $sort = ['type', 'name'];

    protected $columnsToLog = ['description', 'default_value', 'status'];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rules';

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

    public function getRuleTypeName()
    {
        switch ($this->type) {
            case self::EMPLOYEE_TYPE_RULE:
                $rule_type = 'Employee type rule';
                break;
            case self::SECTOR_RULE:
                $rule_type = 'Sector type';
                break;
            case self::COMPANY_RULE:
                $rule_type = 'Company rule';
                break;
            case self::EMPLOYEE_TYPE_SECTOR_RULE:
                $rule_type = 'Employee type to sector rule';
                break;
            default:
                break;
        }
        $this->rule_type = $rule_type;
        // return $rule_type;
    }
}