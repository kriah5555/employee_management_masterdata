<?php

namespace App\Models\Company\Contract;

use App\Models\Contract\ContractType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;
use Spatie\Translatable\HasTranslations;

class CompanyContractTemplate extends BaseModel
{
    use HasFactory, HasTranslations;

    protected $connection = 'tenant';

    protected $table = 'contract_templates';

    protected $primaryKey = 'id';

    protected $fillable = [
        'body',
        'language',
        'status',
        'contract_type_id'
    ];
    public $translatable = ['body'];

    public function contractType()
    {
        return $this->belongsTo(ContractType::class, 'contract_type_id');
    }
}
