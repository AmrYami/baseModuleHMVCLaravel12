<?php

namespace App\Models;

use Users\Models\OfferModel;
use Spatie\Translatable\HasTranslations;

class SpecialityModel extends BaseModel
{
    use HasTranslations;

    protected $table = 'specialities';
    protected $fillable = ['name', 'price', 'description'];


    protected $casts = [
        'name' => 'array',
    ];

    public array $translatable = ['name'];

    public function offers()
    {
        return $this->hasMany(OfferModel::class);
    }
}
