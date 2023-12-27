<?php

namespace App\Models\User;

use App\Models\BaseModel;
use App\Traits\UserAudit;
use App\Models\User\User;
use App\Models\User\MaritalStatus;

class UserFamilyDetails extends BaseModel
{
    use UserAudit;
    
    protected $columnsToLog = [
        'user_id',
        'marital_status_id',
        'dependent_spouse',
        'children',
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $connection = 'userdb';
    protected $table = 'user_family_details';

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
        'marital_status_id',
        'dependent_spouse',
        'children',
        'status',
    ];
    protected $apiValues = [
        'dependent_spouse'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function maritalStatus()
    {
        return $this->belongsTo(MaritalStatus::class);
    }
}
