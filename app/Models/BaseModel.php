<?php

namespace App\Models;

use App\Support\Concerns\UsesHashids;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Translatable\HasTranslations;

class BaseModel extends Model
{
    protected $connection = 'mysql';

    use HasTranslations;
    use LogsActivity;
    use UsesHashids;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(static::class)
            ->logOnly($this->getFillable());
    }

    public function asJson($value, $flags = 0)
    {
        // Your custom implementation here

        // For example, you could call the parent method if needed:
        return parent::asJson($value, $flags);
    }
}
