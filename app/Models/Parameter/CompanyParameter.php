<?php

namespace App\Models\Parameter;

use App\Models\BaseModel;
use App\Traits\UserAudit;

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
        'parameter_name',
        'value',
        'created_by',
        'updated_by'
    ];
}
