<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;
use Users\Models\User;

beforeEach(function (): void {
    if (! file_exists(storage_path('oauth-private.key')) || ! file_exists(storage_path('oauth-public.key'))) {
        Artisan::call('passport:keys', ['--force' => true]);
    }

    app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');
});

afterEach(function (): void {
    Passport::tokensExpireIn(new DateInterval('P1Y'));
    Passport::personalAccessTokensExpireIn(new DateInterval('P1Y'));
    Passport::refreshTokensExpireIn(new DateInterval('P2Y'));
});

test('passport keys exist', function (): void {
    $privateKey = config('passport.private_key') ?: storage_path('oauth-private.key');
    $publicKey = config('passport.public_key') ?: storage_path('oauth-public.key');

    expect($privateKey)->not->toBeEmpty()
        ->and($publicKey)->not->toBeEmpty();

    if (str_starts_with($privateKey, 'file://')) {
        $privateKey = substr($privateKey, 7);
    }

    if (str_starts_with($publicKey, 'file://')) {
        $publicKey = substr($publicKey, 7);
    }

    if (! str_contains($privateKey, 'BEGIN')) {
        expect(is_readable($privateKey))->toBeTrue();
    }

    if (! str_contains($publicKey, 'BEGIN')) {
        expect(is_readable($publicKey))->toBeTrue();
    }
});

test('passport routes are registered', function (): void {
    expect(Route::has('passport.token'))->toBeTrue()
        ->and(Route::has('passport.token.refresh'))->toBeTrue();
});

test('personal access token can be issued', function (): void {
    $user = User::factory()->create();

    $token = $user->createToken('mobile');

    expect($token->accessToken)->not->toBeEmpty()
        ->and($token->token->client->hasGrantType('personal_access'))->toBeTrue();
});

test('valid token authenticates request', function (): void {
    $user = User::factory()->create();
    $token = $user->createToken('mobile')->accessToken;

    $this->withToken($token)
        ->getJson('/api/user')
        ->assertOk()
        ->assertJsonPath('id', $user->id);
});

test('invalid token returns 401', function (): void {
    $this->withToken('not-a-real-token')
        ->getJson('/api/user')
        ->assertUnauthorized();
});

test('expired token returns 401', function (): void {
    Passport::personalAccessTokensExpireIn(now()->subMinute());

    $user = User::factory()->create();
    $token = $user->createToken('mobile')->accessToken;

    $this->withToken($token)
        ->getJson('/api/user')
        ->assertUnauthorized();
});
