<?php

namespace App\Models\EmployeeType;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeTypeContract extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_type_contracts';

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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_type_id',
        'contract_type_id',
        'contract_renewal_id',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function employeeType()
    {
        return $this->belongsTo(EmployeeType::class, 'employee_type_id');
    }

    public function contractType()
    {
        return $this->belongsTo(ContractType::class, 'contract_type_id');
    }
    public function getEmployeeTypeContract() {
        $employeeTypeContracts = EmployeeTypeContract::with('employeeType', 'contractType')->get();
    }

    public static function createOrUpdate($name, $description, $status, $createdBy, $updatedBy)
    {
        // Attempt to find an existing record by name
        $existingRecord = self::where('name', $name)->first();

        // If a record exists, update it; otherwise, create a new one
        if ($existingRecord) {
            $existingRecord->update([
                'description' => $description,
                'status' => $status,
                'updated_by' => $updatedBy,
            ]);
        } else {
            self::create([
                'name' => $name,
                'description' => $description,
                'status' => $status,
                'created_by' => $createdBy,
                'updated_by' => $updatedBy,
            ]);
        }
    }
}
