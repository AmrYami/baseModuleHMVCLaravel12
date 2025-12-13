<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Support\Concerns\UsesHashids;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;
use ReflectionClass;

class BaseModel extends Model
{
    protected $connection = 'mysql';


    use LogsActivity;
    use HasTranslations;
    use UsesHashids;


    protected static $logName;

    // Automatically initialize logging
    protected static function boot()
    {
        parent::boot();
        static::retrieved(function ($model) {
            $model->init();
        });
    }

    public function init()
    {
        self::$logName = get_called_class();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->getFillableAttributes());
    }

    /**
     * Get the fillable attributes of the child model dynamically.
     */
    protected function getFillableAttributes(): array
    {
        $reflection = new ReflectionClass($this);
        $property = $reflection->getProperty('fillable');
        $property->setAccessible(true);

        return $property->getValue($this);
    }

    public function asJson($value, $flags = 0)
    {
        // Your custom implementation here

        // For example, you could call the parent method if needed:
        return parent::asJson($value, $flags);
    }
}
