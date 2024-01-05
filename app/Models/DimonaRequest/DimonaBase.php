<?php

namespace App\Models\DimonaRequest;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class DimonaBase extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dimona_base';

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
        'unique_id',
        // 'dimona_type',
        'dimona_channel',
        'employee_id',
        'employee_rsz',
        'status',
        'dimona_code',
        // 'company_vat',
        // 'company_rsz',
        // 'company_smals_id',
        // 'employee_name',
        // 'start_date_time',
        // 'end_date_time',
        // 'send_status',
        // 'response_status',
    ];

    public function longtermDimona()
    {
        return $this->hasMany(EmployeeContractLongDimonas::class, 'dimona_base_id');
    }

    public function planningDimona()
    {
        return $this->hasMany(PlanningDimona::class, 'dimona_base_id');
    }

    public function dimonaDetails()
    {
        return $this->hasMany(DimonaDetails::class, 'dimona_base_id');
    }
}
