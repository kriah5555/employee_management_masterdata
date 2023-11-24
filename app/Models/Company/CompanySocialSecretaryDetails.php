<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Company\Company;
use App\Traits\UserAudit;
use App\Models\BaseModel;
use App\Models\SocialSecretary\SocialSecretary;

class CompanySocialSecretaryDetails extends BaseModel
{
    use HasFactory, SoftDeletes, UserAudit;

    protected $connection = 'master';

    protected $table = 'company_social_secretary_details';

    protected $primaryKey = 'id';

    protected $fillable = [
        'company_id',
        'social_secretary_id',
        'social_secretary_number',
        'contact_email',
        'created_by',
        'updated_by'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function socialSecretary()
    {
        return $this->belongsTo(SocialSecretary::class, 'social_secretary_id');
    }
}
