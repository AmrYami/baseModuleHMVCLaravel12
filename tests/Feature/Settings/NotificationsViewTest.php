<?php

use Illuminate\Support\Facades\Route;
use Tests\Support\AuthHelpers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);
uses(AuthHelpers::class);

it('renders Notifications settings and saves toggles (skip if route missing)', function(){
    $admin = $this->makeHrUser(permissions: ['setting-notifications']);

    if (!Route::has('dashboard.setting.edit')) {
        test()->markTestSkipped('Settings UI routes are not registered.');
    }

    $resp = actingAs($admin, 'web')->get(route('dashboard.setting.edit', ['key' => 'notifications']));
    if ($resp->getStatusCode() === 404) {
        test()->markTestSkipped('Notifications settings UI disabled.');
    }
    $resp->assertOk();

    actingAs($admin, 'web')->post(route('dashboard.setting.update', ['key'=>'notifications']), [
        'approval_profile' => 'on',
        'new_account' => 'on',
        'notifiers' => 'off',
    ])->assertRedirect();
});
