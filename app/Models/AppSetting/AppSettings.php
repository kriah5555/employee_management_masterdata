<?php

namespace App\Models\AppSetting;

use Illuminate\Database\Eloquent\Model;
use App\Models\AppSetting\AppSettingOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppSettings extends Model
{
    use HasFactory;

    protected $connection = 'tenant';

    protected $tabel = 'app_settings';

    protected $primaryKey = 'id';

    protected $fillable=[
        'type',
        'user_id',
        'content_id'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $columnsToLog = [
        'type',
        'user_id',
        'content_id'
    ];

    public function option()
    {
        return $this->belongsTo(AppSettingOptions::class, 'app_setting_options_id');
    }

}
