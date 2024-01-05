<?php

namespace App\Models\DimonaRequest;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Company\Employee\EmployeeContract;

class EmployeeContractLongDimonas extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_contract_long_dimonas';

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
        'employee_contract_id',
        'dimona_base_id',
    ];

    public function dimonaBase()
    {
        return $this->belongsTo(DimonaBase::class, 'dimona_base_id');
    }

    public function EmployeeContractDimonas()
    {
        return $this->belongsTo(EmployeeContract::class, 'employee_contract_id');
    }
}
