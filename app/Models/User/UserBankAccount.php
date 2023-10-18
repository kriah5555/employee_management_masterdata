<?php

namespace App\Models\User;

use App\Models\BaseModel;
use App\Traits\UserAudit;

class UserAddress extends BaseModel
{
    use UserAudit;
    protected static $sort = ['first_name'];
    protected $columnsToLog = [
        'user_id',
        'account_number',
        'bank_card_file_id',
        'status',
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_bank_accounts';

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
        'deleted_at',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'account_number',
        'bank_card_file_id',
        'status',
    ];
}