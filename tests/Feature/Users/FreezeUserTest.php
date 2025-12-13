<?php

use Carbon\Carbon;
use Tests\Support\AuthHelpers;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\put;
use function Pest\Laravel\assertDatabaseHas;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
uses(AuthHelpers::class);

it('shows the freeze form', function () {
    $user = $this->makeHrUser(permissions: ['block-users']);

    actingAs($user, 'web')
        ->get(route('dashboard.users.freeze.form', ['id' => $user->id]))
        ->assertOk()
        ->assertSee('Freeze User')
        ->assertSee('name="mode"', false)
        ->assertSee('name="banned_until"', false);
});

it('freezes user forever', function () {
    $admin = $this->makeHrUser(permissions: ['block-users']);
    $victim = $this->makeHrUser(['user_name' => 'will.be.frozen']);

    actingAs($admin, 'web')
        ->put(route('dashboard.users.freeze', ['id' => $victim->id]), [
            'mode' => 'forever',
        ])
        ->assertRedirect(route('dashboard.users.index'));

    assertDatabaseHas('users', [
        'id' => $victim->id,
        'freeze' => 1,
    ]);
});

it('freezes user until a given date', function () {
    $admin = $this->makeHrUser(permissions: ['block-users']);
    $victim = $this->makeHrUser(['user_name' => 'will.be.timed']);

    $future = Carbon::now()->addDays(2)->startOfHour();

    actingAs($admin, 'web')
        ->put(route('dashboard.users.freeze', ['id' => $victim->id]), [
            'mode' => 'until',
            'banned_until' => $future->toDateTimeLocalString(),
        ])
        ->assertRedirect(route('dashboard.users.index'));

    $fresh = $victim->fresh();
    expect((int) $fresh->freeze)->toBe(0);
    expect($fresh->banned_until)->not()->toBeNull();
});

it('unfreezes a user (clears flags)', function () {
    $admin = $this->makeHrUser(permissions: ['block-users']);
    $victim = $this->makeHrUser(['freeze' => 1]);

    actingAs($admin, 'web')
        ->put(route('dashboard.users.un_freeze', ['id' => $victim->id]))
        ->assertRedirect();

    $fresh = $victim->fresh();
    expect((int) $fresh->freeze)->toBe(0);
    expect($fresh->banned_until)->toBeNull();
});

