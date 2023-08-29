<?php

namespace App\Models\EmployeeType;

use App\Models\BaseModel;

class EmployeeTypeCategory extends BaseModel
{
    protected static $sort = ['name'];

    protected $columnsToLog = ['name', 'description', 'status'];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_type_categories';

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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'created_at'
    ];
}
