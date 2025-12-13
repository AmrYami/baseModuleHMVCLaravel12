<?php

return [
    App\Providers\AliasServiceProvider::class,
    App\Providers\AppServiceProvider::class,
    App\Providers\ViewServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
//    App\Providers\EventServiceProvider::class,
    App\Providers\FortifyServiceProvider::class,
    App\Providers\JetstreamServiceProvider::class,
    Spatie\Html\HtmlServiceProvider::class,
    Users\Providers\UserServiceProvider::class,
    Users\Providers\ViewServiceProvider::class,
    App\Assessments\Support\AssessmentsServiceProvider::class,
    Intervention\Image\ImageServiceProvider::class,

];
