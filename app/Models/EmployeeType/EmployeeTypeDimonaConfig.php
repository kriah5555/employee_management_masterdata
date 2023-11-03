<?php

namespace App\Models\EmployeeType;

use App\Models\EmployeeType\EmployeeType;
use App\Models\BaseModel;
use App\Models\Dimona\DimonaType;

class EmployeeTypeDimonaConfig extends BaseModel
{
    protected $connection = 'master';

    protected static $sort = [];

    protected $columnsToLog = [
        'employee_type_id',
        'dimona_type_id'
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_type_dimona_config';

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
        'employee_type_id',
        'dimona_type_id',
        'created_by',
        'updated_by',
    ];

    public function employeeType()
    {
        return $this->belongsTo(EmployeeType::class);
    }

    public function dimonaType()
    {
        return $this->belongsTo(DimonaType::class);
    }

    public function dimonaTypeValue()
    {
        return $this->belongsTo(DimonaType::class, 'dimona_type_id')->select(['id as value', 'name as label']);
    }
}