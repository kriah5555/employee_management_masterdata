<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FunctionCategory;

class FunctionTitle extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'function_titles';

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
        'name',
        'function_code',
        'description',
        'status',
        'function_category_id',
        'created_by',
        'updated_by',
    ];

    public function createFunctionTitle()
    {
        
    }

    public function updateFunctionTitle()
    {

    }

    public function deleteFunctionTitle()
    {

    }

    public function archiveFunctionTitle()
    {

    }

    public function functionCategory()
    {
        return $this->belongsTo(FunctionCategory::class);
    }
}
