<?php

use App\Models\SettingModel;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\SettingsController;

useRefreshDatabaseConditionally();

it('sends test email to custom address', function () {
    $admin = Users\Models\User::factory()->create(['email' => 'admin@example.com']);
    SettingModel::create(['key' => 'emails', 'value' => json_encode([
        'welcome' => [
            'enabled' => 'on',
            'subject' => 'Welcome Subject',
            'template' => 'send_mail',
            'body' => 'Body for {name}',
        ],
    ])]);

    // fake validation endpoint
    putenv('ZERO_URL=https://validator.example');
    putenv('ZERO_BOUNCE_API=dummy');
    Http::fake([
        'https://validator.example/validate*' => Http::response(['status' => 'valid'], 200),
    ]);

    $controller = app(SettingsController::class);
    $req = \Illuminate\Http\Request::create('/dashboard/setting/emails/test', 'POST', [
        'action' => 'welcome',
        'to' => 'test@example.com',
    ]);
    $req->setUserResolver(fn() => $admin);
    $controller->sendTest('emails', $req);

    Http::assertSent(function ($request) {
        return str_contains($request->url(), '/validate');
    });
});
