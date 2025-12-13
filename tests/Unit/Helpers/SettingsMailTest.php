<?php

use Illuminate\Support\Facades\Http;
use App\Models\SettingModel;

useRefreshDatabaseConditionally();

it('reads settings json safely', function () {
    expect(getSettingJson('emails'))->toBeArray()->toBeEmpty();
    SettingModel::create(['key' => 'emails', 'value' => json_encode(['welcome' => 'on'])]);
    $cfg = getSettingJson('emails');
    expect($cfg)->toBeArray()->toHaveKey('welcome');
});

it('validates email and calls validator endpoint before sending', function () {
    // Arrange a fake validation endpoint
    config()->set('app.url', 'http://localhost');
    putenv('ZERO_URL=https://validator.example');
    putenv('ZERO_BOUNCE_API=dummy');
    Http::fake([
        'https://validator.example/validate*' => Http::response(['status' => 'valid'], 200),
    ]);

    // Also enable welcome emails in settings
    SettingModel::create(['key' => 'emails', 'value' => json_encode(['welcome' => 'on'])]);

    $user = new class {
        public string $email = 'john@example.com';
        public string $name = 'John Doe';
    };

    // Act
    sendActionMail('welcome', $user, ['body' => 'Welcome {name}']);

    // Assert the validator endpoint was called
    Http::assertSent(function ($request) {
        return str_contains($request->url(), '/validate') && $request->method() === 'GET';
    });
});
