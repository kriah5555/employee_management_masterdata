<?php

namespace App\Models\Dimona;

use App\Models\BaseModel;
use App\Models\Planning\PlanningBase;

class PlanningDimona extends BaseModel
{

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
        'planning_id',
        'dimona_id',
    ];

    public function planningBase()
    {
        return $this->belongsTo(PlanningBase::class, 'planning_id');
    }
}
