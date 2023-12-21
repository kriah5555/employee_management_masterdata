<?php

namespace App\Models\AppSetting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSettingOptions extends Model
{
    use HasFactory;

    protected $connnection = 'tenant';

    protected $table = 'app_setting_options';

    protected $fillable = [
        'options'

    ];

    protected $columnsToLog = [
        'type',
        'user_id',
        'content_id'
    ];

}
