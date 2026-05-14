<?php

use App\Providers\AliasServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\AuthServiceProvider;
use App\Providers\FortifyServiceProvider;
use App\Providers\JetstreamServiceProvider;
use App\Providers\ViewServiceProvider;
use Intervention\Image\Laravel\ServiceProvider;
use Spatie\Html\HtmlServiceProvider;
use Users\Providers\UserServiceProvider;

return [
    AliasServiceProvider::class,
    AppServiceProvider::class,
    ViewServiceProvider::class,
    AuthServiceProvider::class,
    //    App\Providers\EventServiceProvider::class,
    FortifyServiceProvider::class,
    JetstreamServiceProvider::class,
    HtmlServiceProvider::class,
    UserServiceProvider::class,
    Users\Providers\ViewServiceProvider::class,
    ServiceProvider::class,

];
