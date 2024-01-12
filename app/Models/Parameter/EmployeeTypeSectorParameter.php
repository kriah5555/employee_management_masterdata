<?php

namespace App\Models\Parameter;

use App\Models\BaseModel;
use App\Traits\UserAudit;
use App\Models\Parameter\Parameter;

class EmployeeTypeSectorParameter extends BaseModel
{
    use UserAudit;

    protected $columnsToLog = ['value'];

    protected $connection = 'master';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_type_sector_parameters';

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
        'employee_type_id',
        'sector_id',
        'value',
        'created_by',
        'updated_by'
    ];
    public function parameter()
    {
        return $this->belongsTo(Parameter::class);
    }
}
