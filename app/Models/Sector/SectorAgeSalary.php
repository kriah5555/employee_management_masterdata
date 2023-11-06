<?php

namespace App\Models\Sector;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SectorAgeSalary extends Model
{
    protected $connection = 'master';

    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sector_age_salary';

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
        'id',
        'sector_id',
        'age',
        'percentage',
        'max_time_to_work',
        'status',
        'created_by',
        'updated_by',
    ];

    public function getMaxTimeToWorkAttribute($value)
    {
        return date('H:i', strtotime($value));
    }
}
