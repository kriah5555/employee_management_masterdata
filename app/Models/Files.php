<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Files extends Model
{
    use HasFactory;

    protected $table = 'files';

    protected $primaryKey = 'id';

    protected $fillable = [
        'file_name',
        'file_path',
    ];

    public function getFilePathAttribute($value)
    {
        return asset(Storage::url($value));
    }
}
