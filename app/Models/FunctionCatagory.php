<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FunctionCatagoryToTitles;

class FunctionCatagory extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'function_catagory';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'function_catagory_id';

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
        'function_catagory_id',
        'sector_id',
        'function_catagory_name',
        'description',
        'status',
        'created_by',
        'updated_by',
    ];
}
