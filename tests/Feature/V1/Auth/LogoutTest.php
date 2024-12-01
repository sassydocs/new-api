<?php

use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\deleteJson;

it('Requires login', function () {
    deleteJson('/auth')
        ->assertUnauthorized();
});

it('I can logout', function () {
    $user = User::factory()
        ->create();

    actingAs($user)
        ->deleteJson('/auth')
        ->assertSuccessful();
});
