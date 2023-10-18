<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Traits\UserAudit;

class Reason extends BaseModel
{
    use UserAudit;
    protected static $sort = ['name'];
    protected $columnsToLog = [
        'name',
        'category',
        'status'
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'reasons';

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
        'updated_at'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'category',
        'status',
        'created_by',
        'updated_by',
    ];
}