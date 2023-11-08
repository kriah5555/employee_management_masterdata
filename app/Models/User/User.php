<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Traits\HasPermissions;
use App\Models\User\UserBasicDetails;
use App\Models\User\UserBankAccount;
use App\Models\User\UserFamilyDetails;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasPermissions;

    protected $table = 'users';
    protected $connection = 'userdb';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'social_security_number',
        'password',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected static function booted()
    {
        static::addGlobalScope('sort', function ($query) {
            $query->orderBy('username', 'asc');
        });
    }

    public function isActive(): bool
    {
        return $this->status;
    }

    public function findForPassport($username)
    {
        return $this->where('username', $username)->first();
    }
    public function userBasicDetails()
    {
        return $this->hasOne(UserBasicDetails::class);
    }
    public function userContactDetails()
    {
        return $this->hasOne(UserContactDetails::class);
    }
    public function userAddress()
    {
        return $this->hasOne(UserAddress::class);
    }
    public function userBankAccount()
    {
        return $this->hasOne(UserBankAccount::class);
    }
    public function userFamilyDetails()
    {
        return $this->hasOne(UserFamilyDetails::class);
    }
    // public function employeeProfiles()
    // {
    //     return $this->hasMany(EmployeeProfile::class)
    //         ->where('status', true);
    // }
}
