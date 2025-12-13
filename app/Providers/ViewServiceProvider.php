<?php

namespace App\Providers;

use Illuminate\Http\Request;
use App\Models\NotificationModel;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Repositories\RolesRepositoryShow;
use Users\Repositories\RequestRepositoryShow;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        View::composer(['users::users.role_field'], function($view){
            $repo = app(RolesRepositoryShow::class);
            $roleCollection = $repo->find_by([]);
            $roles = $roleCollection
                ->mapWithKeys(fn($role) => [hid($role->id) => $role->name])
                ->toArray();
            $userRoleId = optional($roleCollection->firstWhere('name', 'User'))->id;
            $userRoleId = $userRoleId ? hid($userRoleId) : null;
            $view->with([
                'roles' => $roles,
                'userRoleId' => $userRoleId,
            ]);
        });


        View::composer(['users::users.list_cycle_requests'], function ($view) {
            $repo = app(RequestRepositoryShow::class);
            $request = request();
            $userParam = $request->route('user');
            $userId = $userParam instanceof \Illuminate\Database\Eloquent\Model
                ? $userParam->getKey()
                : (hid_decode($userParam) ?? $userParam);
            $request->merge(['request_to' => auth()->user()->id, 'user_id' => $userId]);
            $cycleRequests = $repo->find_by($request->only(['request_to', 'user_id']));
            $view->with('cycleRequests', $cycleRequests);

        });
        View::composer(['users::users.list_my_requests'], function ($view) {
            $repo = app(RequestRepositoryShow::class);
            $request =  request();
            $userParam = $request->route('user');
            $userId = $userParam instanceof \Illuminate\Database\Eloquent\Model
                ? $userParam->getKey()
                : (hid_decode($userParam) ?? $userParam);
            $request->merge(['request_from' => auth()->user()->id, 'user_id' => $userId]);
            $myRequests = $repo->myRequests($request->only(['request_from', 'user_id']));
            $view->with('myRequests', $myRequests);
        });
        View::composer(['dashboard.mt.layout.header'], function ($view) {
            $user = auth()->user();
            $canReview = false;
            if ($user) {
                try {
                    $canReview = $user->hasAnyPermission([
                        'approve-profile',
                    ]);
                } catch (\Throwable $e) {
                    $canReview = false;
                }
            }

            if ($canReview) {
                $notifications = isset($user?->id) ? NotificationModel::orderBy('seen', 'desc')->get() : collect([]);
                $countNotifications = isset($user?->id) ? NotificationModel::where([['seen', '=', '0']])->count() : 0;
            } else {
                $notifications = isset($user?->id) ? NotificationModel::where('user_id', $user->id)->orderBy('seen', 'desc')->get() : collect([]);
                $countNotifications = isset($user?->id) ? NotificationModel::where([['seen', '=', '0'], ['user_id', '=', $user->id]])->count() : 0;
            }
            $view->with('notifications', $notifications)->with('countNotifications', $countNotifications);
        });

    }
}
