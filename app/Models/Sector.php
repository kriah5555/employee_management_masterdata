<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sectors';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'sector_id';

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
        'sector_id',
        'sector_name',
        'sector_number',
        'description',
        'status',
        'created_by',
        'updated_by',
    ];
  


}
