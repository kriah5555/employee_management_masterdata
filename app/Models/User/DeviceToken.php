<?php

namespace App\Models\User;


use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeviceToken extends Model
{
    protected $table = 'device_tokens';
    protected $connection = 'userdb';


    use HasFactory;

    protected $fillable = ['device_token', 'unique_identifier'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
