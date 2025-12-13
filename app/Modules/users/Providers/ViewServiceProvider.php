<?php

namespace Users\Providers;
use App\Models\CountryModel;
use App\Models\LanguageModel;
use App\Models\SpecialityModel;
use Users\Models\CompanyModel;
use Users\Models\Permissions;
use Users\Models\Role;
use Illuminate\Support\ServiceProvider;
use View;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer(['users::roles.fields'], function ($view) {
            $role = Role::where('id', request()->role)->first();
            $guard = $role->guard_name ?? config('auth.defaults.guard', 'web');
            $allPermissions = Permissions::where('guard_name', $guard)->get();

            $user = auth()->user();
            if ($user && !$user->hasRole('CRM Admin')) {
                $allowed = $user->getAllPermissions()->pluck('name');
                $allPermissions = $allPermissions->whereIn('name', $allowed);
            }

            $view->with('allPermissions', $allPermissions);
        });

        View::composer(['users::users.form_fields', 'users::users.role_field', 'users::users.create', 'users::users.edit'], function ($view) {
            $countries = CountryModel::all()->mapWithKeys(function ($country) {
                return [$country->key => $country->getTranslation('name', app()->getLocale())];
            });
            $view->with('countries', $countries);

            // Limit role options to roles created by the authed user (unless CRM Admin)
            $user = auth()->user();
            $roles = Role::query()
                ->where('guard_name', config('auth.defaults.guard', 'web'))
                ->where('name', '!=', 'CRM Admin');

            if ($user && !$user->hasRole('CRM Admin')) {
                $roles->where('created_by', $user->id);
            }

            $view->with('roles', $roles->pluck('name', 'id')->toArray());
        });
    }
}
