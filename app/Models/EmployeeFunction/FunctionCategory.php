<?php

namespace App\Models\EmployeeFunction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sector\Sector;
use Illuminate\Database\Eloquent\SoftDeletes;

class FunctionCategory extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'function_category';

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

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sector_id',
        'name',
        'description',
        'category',
        'status',
        'created_by',
        'updated_by',
    ];

    public function sector()
    {
        return $this->belongsTo(Sector::class)->withTrashed();
    }

    protected static function booted()
    {
        static::addGlobalScope('sort', function ($query) {
            $query->orderBy('name', 'asc');
        });
    }
}
