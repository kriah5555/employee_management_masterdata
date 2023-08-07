<?php

namespace App\Models\EmployeeType;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\EmployeeType\EmployeeTypeCategory;
use App\Models\EmployeeType\EmployeeTypeContract;
use App\Models\EmployeeType\EmployeeTypeDimona;
use App\Models\Contracts\ContractRenewal;
use App\Models\Contracts\ContractTypeList;
use App\Models\Contracts\ContractTypes;

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
        'status',
        'created_by',
        'updated_by',
    ];

    public function employeeTypeCategory()
    {
        return $this->belongsTo(EmployeeTypeCategory::class, 'employee_type_categories_id');
    }
}
