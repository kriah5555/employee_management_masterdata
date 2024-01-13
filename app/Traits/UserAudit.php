<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use App\Models\User\User;

trait UserAudit
{
    public static function bootUserAudit()
    {
        static::creating(function ($model) {
            $user = Auth::id();
            if ($user) {
                $model->created_by = $user;
                $model->updated_by = $user;
            }
        });

        static::updating(function ($model) {
            if (!$model->isDirty()) {
                $user = Auth::id();
                if ($user) {
                    $model->updated_by = $user;
                }
            }
        });
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
