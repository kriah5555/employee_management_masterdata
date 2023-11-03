<?php

namespace App\Models\Employee;

use App\Models\BaseModel;
use App\Traits\UserAudit;

class CommuteType extends BaseModel
{
    use UserAudit;

    protected $connection = 'tenant';

    protected static $sort = ['sort_order', 'name'];
    protected $columnsToLog = [
        'sort_order',
        'name',
        'status'
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $connection = 'master';
    protected $table = 'commute_types';

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
        'sort_order',
        'name',
        'status'
    ];
}
