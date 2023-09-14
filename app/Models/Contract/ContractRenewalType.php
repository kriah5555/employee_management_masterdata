<?php

namespace App\Models\Contract;

use App\Models\BaseModel;
use App\Models\ContractType;

class ContractRenewalType extends BaseModel
{
    protected static $sort = ['sort_order', 'name'];
    protected $columnsToLog = ['key', 'name', 'sort_order', 'status'];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'contract_renewal_types';

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
        'key',
        'sort_order',
        'name',
        'status',
        'created_by',
        'updated_by'
    ];

    public function contractType()
    {
        return $this->hasMany(ContractType::class)
            ->where('status', true);
    }
}
