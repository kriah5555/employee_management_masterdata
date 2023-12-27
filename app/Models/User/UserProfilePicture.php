<?php

namespace App\Models\User;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserProfilePicture extends Model
{
    protected $connection = 'userdb';

    use HasFactory;

    protected $columnsToLog = [
        'user_id',
        'image_path',
        'image_name',
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_profile_pictures';

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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'image_path',
        'image_name',
    ];
    protected $apiValues = [
        'user_id',
        'image_path',
        'image_name',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
