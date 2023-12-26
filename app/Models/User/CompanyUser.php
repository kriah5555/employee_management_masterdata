<?php

namespace App\Models\User;

use App\Models\BaseModel;
use App\Models\Company\Company;
use App\Models\Company\Employee\EmployeeProfile;
use App\Traits\UserAudit;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Traits\HasPermissions;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class CompanyUser extends BaseModel
{
    use HasFactory, Notifiable, HasRoles, HasPermissions, UserAudit, SoftDeletes;
    protected $connection = 'master';

    protected $guard_name = 'api';

    protected $columnsToLog = [
        'company_id',
        'user_id'
    ];
    /**
     * The table associated with the model.
     *
     * @var string
     */
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function getCompanyDetails($userId)
    {
        return $this->where('user_id', $userId);
    }
}
