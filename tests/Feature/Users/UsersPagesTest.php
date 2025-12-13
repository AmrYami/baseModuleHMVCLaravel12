<?php

use Tests\Support\AuthHelpers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);
uses(AuthHelpers::class);

it('renders Users index for HR with permission', function () {
    $hr = $this->makeHrUser(permissions: ['list-users']);

    actingAs($hr, 'web')
        ->get(route('dashboard.users.index'))
        ->assertOk()
        ->assertSee('Users');
});

it('renders Users create page', function () {
    $hr = $this->makeHrUser(permissions: ['create-users']);

    actingAs($hr, 'web')
        ->get(route('dashboard.users.create'))
        ->assertOk()
        ->assertSee('Create New User')
        ->assertSee('name="user_name"', false)
        ->assertSee('name="email"', false)
        ->assertSee('name="mobile"', false);
});

it('renders Users edit page', function () {
    $hr = $this->makeHrUser(permissions: ['edit-users']);
    $victim = $this->makeHrUser(['user_name' => 'edit.target', 'email' => 'edit.target@example.com']);

    actingAs($hr, 'web')
        ->get(route('dashboard.users.edit', ['user' => $victim->id]))
        ->assertOk()
        ->assertSee('Create New User') // title component reused
        ->assertSee('name="user_name"', false)
        ->assertSee('name="email"', false);
});

