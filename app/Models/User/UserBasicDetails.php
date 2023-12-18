<?php

namespace App\Models\User;

use App\Models\BaseModel;
use App\Traits\UserAudit;
use App\Models\User\User;

class UserBasicDetails extends BaseModel
{
    use UserAudit;
    protected $connection = 'userdb';

    protected static $sort = ['first_name'];

    protected $columnsToLog = [
        'user_id',
        'first_name',
        'last_name',
        'nationality',
        'gender_id',
        'date_of_birth',
        'place_of_birth',
        'license_expiry_date',
        'language',
        'extra_info'
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */

    protected $table = 'user_basic_details';

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
        'date_of_birth',
        'license_expiry_date'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'nationality',
        'gender_id',
        'date_of_birth',
        'place_of_birth',
        'license_expiry_date',
        'language',
        'extra_info'
    ];

    protected $apiValues = [
        'first_name',
        'last_name',
        'nationality',
        'date_of_birth',
        'place_of_birth',
        'gender_id',
        'license_expiry_date',
        'language',
        'extra_info'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function gender()
    {
        return $this->belongsTo(Gender::class);
    }
}
