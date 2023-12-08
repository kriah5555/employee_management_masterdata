<?php

namespace App\Models\Company\Contract;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseModel;
use App\Models\EmployeeType\EmployeeType;
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
        'employee_type_id'
    ];
    public $translatable = ['body'];

    public function employeeType()
    {
        return $this->belongsTo(EmployeeType::class, 'employee_type_id');
    }
}
