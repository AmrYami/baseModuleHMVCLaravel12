<?php

namespace Users\Models;

use App\Models\BaseModel;
use App\Models\SpecialityModel;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;
use Users\Models\User;

class CompanyModel extends BaseModel implements HasMedia
{


    use InteractsWithMedia;
    use HasTranslations;

    protected $table = "companies";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

//
//Doctor Video URLs(multi)
    /**
     * The attributes that are translatable.
     *
     * @var array
     */
//    public $translatable = [
//        'title',
//    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    protected $dates = [
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
    ];


    public function users(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class);
    }

}

