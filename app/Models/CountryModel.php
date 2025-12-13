<?php

namespace App\Models;

use Spatie\Translatable\HasTranslations;

class CountryModel extends BaseModel
{
    use HasTranslations;

    protected $table = 'countries';
    protected $fillable = ['key', 'name'];
    public $translatable = ['name'];
    protected $casts = [
        'name' => 'array',
    ];
    /**
     * Override the default “name” attribute so that:
     *  - we decode the stored JSON,
     *  - pick the current app locale,
     *  - fall back to English (or the first element) if missing.
     */
//    public function getNameAttribute($value)
//    {
//        // Decode to PHP array
//        $names = json_decode($value, true) ?: [];
//
//        // Pull the current locale (e.g. "en" or "ar")
//        $locale = app()->getLocale();
//
//        // Return the matching name, or fallback to 'en', or finally any entry
//        if (isset($names[$locale])) {
//            return $names[$locale];
//        }
//
//        if (isset($names['en'])) {
//            return $names['en'];
//        }
//
//        // If all else fails, return the first value in the array
//        return array_values($names)[0] ?? null;
//    }

}
