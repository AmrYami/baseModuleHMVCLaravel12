<?php

namespace Users\Models;

use App\Models\BaseModel;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Topic extends BaseModel implements HasMedia
{


    use InteractsWithMedia;
    use HasTranslations;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [


        'title', 'content', 'order', 'active'
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

}

