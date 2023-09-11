<?php

namespace App\Models\Rule;

use App\Models\BaseModel;
use App\Traits\UserAudit;

class EmployeeTypeRule extends BaseModel
{
    use UserAudit;
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
        'default_value',
        'status',
        'created_by',
        'updated_by'
    ];
}
