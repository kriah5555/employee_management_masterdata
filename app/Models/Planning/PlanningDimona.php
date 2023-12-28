<?php

namespace App\Models\Planning;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Planning\PlanningBase;
use App\Models\DimonaRequest\DimonaBase;

class PlanningDimona extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'planning_dimonas';

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
        'planning_base_id',
        'dimona_base_id',
    ];

    public function dimonaBase()
    {
        return $this->belongsTo(DimonaBase::class, 'dimona_base_id');
    }

    public function planningBase()
    {
        return $this->belongsTo(PlanningBase::class, 'planning_base_id');
    }
}
