<?php

namespace App\Models\Email;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class EmailTemplate extends Model
{
    use HasTranslations, SoftDeletes;

    protected $connection = 'master';

    protected $table = 'email_templates';
    
    public $translatable = ['body', 'subject'];

    protected $fillable = [
        'template_type',
        'status',
        'body',
        'subject'
    ];

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
}