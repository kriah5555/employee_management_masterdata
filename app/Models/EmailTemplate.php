<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class EmailTemplate extends Model
{
    use HasTranslations, SoftDeletes;

    public $translatable = ['body', 'subject'];

    protected $fillable = [
        'template_type',
        'status',
        'body',
        'subject'
    ];
}