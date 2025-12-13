<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Spatie\Translatable\HasTranslations;

class LanguageModel extends Model
{
    use HasTranslations;

    protected $table = 'languages';
    protected $fillable = ['key', 'name'];
    public $translatable = ['name'];
}
