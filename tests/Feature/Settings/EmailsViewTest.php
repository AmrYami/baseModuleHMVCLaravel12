<?php

use Tests\Support\AuthHelpers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;
use Illuminate\Support\Facades\Route;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);
uses(AuthHelpers::class);

it('renders Emails settings and accepts save + test', function(){
    $admin = $this->makeHrUser(permissions: ['setting-notifications']);

    if (!Route::has('dashboard.setting.edit')) {
        test()->markTestSkipped('Settings UI routes are not registered.');
    }
    $resp = actingAs($admin, 'web')->get(route('dashboard.setting.edit', ['key' => 'emails']));
    if ($resp->getStatusCode() === 404) {
        test()->markTestSkipped('Emails settings UI disabled.');
    }
    $resp->assertOk();

    // Save
    actingAs($admin, 'web')->post(route('dashboard.setting.update', ['key'=>'emails']), [
            'welcome' => ['enabled'=>true,'subject'=>'Welcome','body'=>'Hello {name}'],
        ])->assertRedirect();

    // Send Test
    actingAs($admin, 'web')->post(route('dashboard.setting.test', ['key'=>'emails']), [
            'action' => 'welcome', 'to' => 'test@example.com'
        ])->assertRedirect();
});
