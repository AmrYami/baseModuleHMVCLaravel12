<?php

namespace App\Models;

use App\Support\Concerns\LogsModelActivity;
use App\Support\Concerns\UsesHashids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Translatable\HasTranslations;

class BaseAuthModel extends Authenticatable
{
    protected $connection = 'mysql';

    use HasTranslations;
    use LogsModelActivity;
    use UsesHashids;

    public function asJson($value, $flags = 0)
    {
        // Your custom implementation here

        // For example, you could call the parent method if needed:
        return parent::asJson($value, $flags);
    }
}
