<?php

namespace App\Models\Contract;

use App\Models\BaseModel;
use App\Models\Contract\ContractRenewalType;

class ContractType extends BaseModel
{
    protected static $sort = ['name'];

    protected $columnsToLog = ['name', 'description', 'contract_renewal_type_id', 'status'];
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
        'contract_renewal_type_id',
        'status',
        'created_by',
        'updated_by'
    ];

    public function contractRenewalType()
    {
        return $this->belongsTo(ContractRenewalType::class)
        ->where('status', true);
    }

    public function contractRenewalTypeValue()
    {
        return $this->belongsTo(ContractRenewalType::class, 'contract_renewal_type_id')
        ->select('id as value', 'name as label')
        ->where('status', true);
    }
}
