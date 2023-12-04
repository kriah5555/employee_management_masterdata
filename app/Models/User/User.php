<?php

namespace App\Models\User;

use App\Models\User\UserBankAccount;
use App\Models\User\UserBasicDetails;
use App\Models\User\UserFamilyDetails;
use Spatie\Permission\Traits\HasRoles;
use App\Models\User\UserContactDetails;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasPermissions;
use App\Models\Company\Employee\EmployeeProfile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasPermissions;

    protected $connection = 'userdb';

    protected $guard_name = 'api';
    protected $table = 'users';
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

    public function employeeProfiles()
    {
        return $this->hasMany(EmployeeProfile::class)
            ->where('status', true);
    }

    public function companyUser()
    {
        return $this->hasMany(CompanyUser::class);
    }
    public function userBankDetails($userId)
    {
        return $this->hasOne(UserBankAccount::class)->where('user_id', $userId);
    }

    public function userBasicDetailsById($user_id)
    {
        return $this->hasOne(UserBasicDetails::class)->where('user_id', $user_id);
    }

    public function userAddressById($user_id)
    {
        return $this->hasOne(UserAddress::class)->where('user_id', $user_id);
    }

    public function userContactById($user_id)
    {
        return $this->hasOne(UserContactDetails::class)->where('user_id', $user_id);
    }

    public function companyUser()
    {
        return $this->hasMany(CompanyUser::class);
    }
}
