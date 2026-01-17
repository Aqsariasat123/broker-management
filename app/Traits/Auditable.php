<?php

namespace App\Traits;

use App\Models\AuditLog;

trait Auditable
{
    /**
     * Boot the trait
     */
    public static function bootAuditable()
    {
        static::created(function ($model) {
            AuditLog::log('create', $model, null, $model->getAttributes());
        });

        static::updated(function ($model) {
            AuditLog::log('update', $model, $model->getOriginal(), $model->getChanges());
        });

        static::deleted(function ($model) {
            AuditLog::log('delete', $model, $model->getAttributes(), null);
        });
    }
}

