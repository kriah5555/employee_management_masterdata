<?php

namespace App\Models\User;

use App\Models\BaseModel;
use App\Traits\UserAudit;

class CompanyUser extends BaseModel
{
    use UserAudit;
    protected $columnsToLog = [
        'company_id',
        'user_id'
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $connection = 'master';
    protected $table = 'company_users';

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
        'company_id',
        'user_id'
    ];
}
