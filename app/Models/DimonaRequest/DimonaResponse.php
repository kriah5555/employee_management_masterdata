<?php

namespace App\Models\DimonaRequest;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DimonaResponse extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dimona_responses';

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
        'dimona_details_id',
        'result',
        'dimona_period_id',
        'registration_id',
        'smals_response',
    ];

    public function dimonaDetails()
    {
        return $this->belongsTo(DimonaDetails::class, 'dimona_details_id');
    }
}
