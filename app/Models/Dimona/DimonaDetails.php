<?php

namespace App\Models\Dimona;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DimonaDetails extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dimona_details';

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
        'dimona_base_id',
        'dimona_type',
        'status',
        'start_date_time',
        'end_date_time'
    ];

    public function dimonaBase()
    {
        return $this->belongsTo(DimonaBase::class, 'dimona_base_id');
    }

    public function dimonaError()
    {
        return $this->hasMany(DimonaError::class, 'dimona_details_id');
    }

    public function dimonaResponse()
    {
        return $this->hasMany(DimonaResponse::class, 'dimona_details_id');
    }
}
