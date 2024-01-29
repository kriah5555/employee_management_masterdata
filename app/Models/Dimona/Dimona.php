<?php

namespace App\Models\Dimona;

use App\Models\BaseModel;
use App\Models\Dimona\DimonaDeclaration;
use App\Models\Dimona\LongTermDimona;

class Dimona extends BaseModel
{

    protected $connection = 'tenant';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dimonas';

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
        'type',
        'dimona_period_id',
    ];

    public function dimonaDeclarations()
    {
        return $this->hasMany(DimonaDeclaration::class, 'dimona_id');
    }

    public function longtermDimona()
    {
        return $this->hasOne(LongTermDimona::class, 'dimona_id');
    }

    public function planningDimona()
    {
        return $this->hasOne(PlanningDimona::class, 'dimona_id');
    }
}
