<?php

namespace App\Models\User;

use App\Models\BaseModel;
use App\Traits\UserAudit;

class UserContactDetails extends BaseModel
{
    use UserAudit;

    protected $connection = 'userdb';

    protected $columnsToLog = [
        'user_id',
        'email',
        'phone_number'
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_contact_details';

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
        'deleted_at'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'email',
        'phone_number'
    ];
    protected $apiValues = [
        'email',
        'phone_number'
    ];
}
