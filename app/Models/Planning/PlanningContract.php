<?php

namespace App\Models\Planning;

use App\Models\Contract\ContractType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class PlanningContract extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'tenant';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'planning_contracts';

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
        'file_id',
        'planning_base_id',
        'contract_type_id',
        'contract_status',
        'status', 
        'created_by',
        'updated_by',
    ];

    protected $appends = ['file_url'];

    public function plan()
    {
        return $this->belongsTo(PlanningBase::class, 'planning_base_id');
    }

    public function files()
    {
        return $this->belongsTo(Files::class, 'file_id');
    }

    public function getFileUrlAttribute()
    {
        return env('CONTRACTS_URL') . '/' . $this->files->file_path;
    }
    
    public function contractType()
    {
        return $this->belongsTo(ContractType::class, 'contract_type_id');
    } 
}
