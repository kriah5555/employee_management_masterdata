<?php

namespace App\Models\EmployeeType;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\EmployeeType\EmployeeTypeCategory;
use App\Models\EmployeeType\EmployeeTypeContract;
use App\Models\EmployeeType\EmployeeTypeDimona;
use App\Models\Contracts\ContractRenewal;
// use App\Models\Contracts\ContractTypeList;
use App\Models\Contracts\ContractType;
use App\Models\Dimona\DimonaType;

class EmployeeType extends Model
{
    use HasFactory, SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_types';

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
        'deleted_at'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'key',
        'description',
        "employee_type_category_id",
        'status',
        'created_by',
        'updated_by',
    ];

    public function employeeTypeCategory()
    {
        return $this->belongsTo(EmployeeTypeCategory::class);
    }

    public function getEmployeeTypeOptions()
    {
        $options['contract_types'] = $this->getDataFromQuery(ContractType::select(['id', 'name as value', 'contract_type_key']));
        $options['contract_renewal'] = $this->getDataFromQuery(ContractRenewal::select(['id', 'name as value', 'duration']));
        $options['dimona_type'] = $this->getDataFromQuery(DimonaType::select(['id', 'name as value', 'dimona_type_key']));
        return $options;
    }

    public function getDataFromQuery($query)
    {
        return $query->where('status', '=', true)
        ->get()
        ->toArray();
    }
    public function contractTypes()
    {
        return $this->belongsToMany(ContractType::class, 'contract_type_employee_type');
    }

    public function isDeleted(): bool
    {
        return $this->deleted_at !== null;
    }

    public function isActive(): bool
    {
        return $this->status;
    }
}
