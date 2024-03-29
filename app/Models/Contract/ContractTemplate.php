<?php

namespace App\Models\Contract;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;
use App\Models\EmployeeType\EmployeeType;
use App\Models\SocialSecretary\SocialSecretary;
use Spatie\Translatable\HasTranslations;

class ContractTemplate extends BaseModel
{
    use HasFactory, HasTranslations;

    protected $connection = 'master';

    protected $table = 'contract_templates';

    protected $primaryKey = 'id';

    protected $fillable = [
        'body',
        'status',
        'contract_type_id',
    ];
    public $translatable = ['body'];

    public function contractType()
    {
        return $this->belongsTo(ContractType::class, 'contract_type_id');
    }

    public function socialSecretary()
    {
        return $this->belongsToMany(SocialSecretary::class, 'contract_template_social_secretary');
    }
}
