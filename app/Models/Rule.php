<?php

namespace App\Models;

use App\Models\BaseModel;

class Rule extends BaseModel
{

    const EMPLOYEE_TYPE_RULE = 1;
    const SECTOR_RULE = 2;
    const EMPLOYEE_TYPE_SECTOR_RULE = 3;
    const COMPANY_RULE = 4;
    const LOCATION_RULE = 5;

    protected static $sort = ['name'];

    protected $columnsToLog = ['name', 'description', 'type', 'default_value', 'status'];
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
        'default_value',
        'status',
        'created_by',
        'updated_by'
    ];

    public function setType($value)
    {
        $validStatuses = [
            self::EMPLOYEE_TYPE_RULE,
            self::SECTOR_RULE,
            self::EMPLOYEE_TYPE_SECTOR_RULE,
            self::COMPANY_RULE,
            self::LOCATION_RULE,
        ];

        if (in_array($value, $validStatuses)) {
            $this->attributes['status'] = $value;
        }
    }
}