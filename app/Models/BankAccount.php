<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Traits\UserAudit;

class BankAccount extends BaseModel
{
    use UserAudit;
    protected static $sort = ['bank_account_number'];
    protected $columnsToLog = [
        'bank_account_number',
        'verified',
        'verification_file_id',
        'status'
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $connection = 'master';
    protected $table = 'bank_accounts';

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
        'bank_account_number',
        'verified',
        'verification_file_id',
        'status'
    ];
}
