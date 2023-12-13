<?php

namespace App\Services\Company;

use App\Models\User\User;
use App\Models\AppSetting\AppSettings;
use App\Models\AppSetting\AppSettingOptions;

class AppSettingsService
{
    public function getAppSettingsOptions($id)
    {
        //need to check role as per user id
        $user_roles = User::find($id)->select('roles');
        $user_roles=2;
        return AppSettingOptions::where('role_id',$id)->get()->toArray();
    }

    public function updateAppSettingsOptions($options_id, $values)
    {
        try {
            return AppSettings::where('app_setting_options_id', '=', $options_id)->update($values);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}
