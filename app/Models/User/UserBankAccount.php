<?php

namespace App\Models\User;

use App\Models\BaseModel;
use App\Traits\UserAudit;
use App\Models\User\User;

class UserBankAccount extends BaseModel
{
    use UserAudit;
    protected $columnsToLog = [
        'user_id',
        'account_number',
        'bank_card_file_id'
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $connection = 'userdb';
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
        'bank_card_file_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
