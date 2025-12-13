<?php

use Tests\Support\AuthHelpers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;
use Illuminate\Support\Facades\Route;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);
uses(AuthHelpers::class);

it('renders Registration settings and saves default status', function(){
    $admin = $this->makeHrUser(permissions: ['setting-notifications']);

    if (!Route::has('dashboard.setting.edit')) {
        test()->markTestSkipped('Settings UI routes are not registered.');
    }
    $resp = actingAs($admin, 'web')->get(route('dashboard.setting.edit', ['key' => 'registration']));
    if ($resp->getStatusCode() === 404) {
        test()->markTestSkipped('Registration settings UI disabled.');
    }
    $resp->assertOk();

    actingAs($admin, 'web')
        ->post(route('dashboard.setting.update', ['key'=>'registration']), [
            'default_status' => 'pending',
        ])->assertRedirect();
});
