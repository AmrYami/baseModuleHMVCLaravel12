<?php

use Tests\Support\AuthHelpers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);
uses(AuthHelpers::class);

it('shows HR menu items when permissions present', function(){
    $perms = ['hr.hospitals.index','hr.datasets.index'];
    $user = $this->makeHrUser(permissions: $perms);
    actingAs($user, 'web')->get(route('dashboard'))
        ->assertOk()
        ->assertSee('HR');
});
