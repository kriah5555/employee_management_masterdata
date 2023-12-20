<?php
namespace App\Traits;

use Carbon\Carbon;

trait DatabaseFieldDateFormat
{
    public static function bootDatabaseFieldDateFormat()
    {
        static::saving(function ($model) {
            // Loop through date fields and format them as Y-m-d
            foreach ($model->dates as $dateField) {
                if (in_array($dateField, ['date_of_birth', 'license_expiry_date', 'start_date', 'end_date']) && !empty($model->{$dateField})) {
                    $model->{$dateField} = Carbon::parse($model->{$dateField})->format('Y-m-d');
                }
            }
        });
    }
}
