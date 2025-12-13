<?php

namespace App\Providers;

use App\Helpers\MoreImplementation;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(MoreImplementation::class, function () {
            return new MoreImplementation();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }

        Schema::defaultStringLength(191);

        View::addNamespace('mail', resource_path('views/emails'));

        if ($this->app->runningUnitTests()) {
            $compiled = storage_path('framework/testing-views');
            if (!is_dir($compiled)) {
                @mkdir($compiled, 0777, true);
            }
            config(['view.compiled' => $compiled]);
        }
    }
}
