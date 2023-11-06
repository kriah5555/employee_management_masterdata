<?php

namespace App\Models\User;

use App\Models\BaseModel;
use App\Traits\UserAudit;
use App\Models\User\User;

class UserAddress extends BaseModel
{
    use UserAudit;
    protected static $sort = ['first_name'];
    protected $columnsToLog = [
        'user_id',
        'street_house_no',
        'postal_code',
        'city',
        'country',
        'latitude',
        'longitude'
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $connection = 'userdb';
    protected $table = 'user_addresses';

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
        'street_house_no',
        'postal_code',
        'city',
        'country',
        'latitude',
        'longitude'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
