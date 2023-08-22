<?php

namespace App\Models\Contracts;

use App\Models\BaseModel;

class ContractType extends BaseModel
{
    protected $columnsToLog = ['name', 'description', 'renewal', 'status'];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'contract_types';

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
        'renewal',
        'status',
        'created_by',
        'updated_by'
    ];
}
