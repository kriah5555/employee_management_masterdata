<?php

namespace App\Models\Company\Employee;

use App\Models\BaseModel;
use App\Models\Company\Employee\EmployeeProfile;
use App\Traits\UserAudit;

class EmployeeInvitation extends BaseModel
{
    use UserAudit;

    protected $columnsToLog = ['data', 'invitation_status'];
    protected $connection = 'tenant';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_invitations';

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
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'token',
        'data',
        'invitation_status',
        'expire_at',
    ];
    public function inviter()
    {
        return $this->belongsTo(EmployeeProfile::class, 'invited_by');
    }
}
