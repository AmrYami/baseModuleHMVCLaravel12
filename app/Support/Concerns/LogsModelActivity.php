<?php

namespace App\Support\Concerns;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use ReflectionClass;

/**
 * Reusable activity logging setup for models that cannot extend BaseModel directly.
 */
trait LogsModelActivity
{
    use LogsActivity;

    protected static $logName;

    protected static function bootLogsModelActivity()
    {
        static::retrieved(function ($model) {
            $model->initLogName();
        });
    }

    protected function initLogName(): void
    {
        self::$logName = get_called_class();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->getFillableAttributes());
    }

    /**
     * Get fillable attributes for the concrete model.
     */
    protected function getFillableAttributes(): array
    {
        $reflection = new ReflectionClass($this);
        if (!$reflection->hasProperty('fillable')) {
            return [];
        }
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);
        return $property->getValue($this) ?? [];
    }
}
