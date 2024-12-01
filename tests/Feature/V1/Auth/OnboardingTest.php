<?php

use App\Models\User;
use function Pest\Laravel\actingAs;

it('blocks users who are already logged in', function () {
    actingAs(User::factory()->create())
        ->postJson('/auth/onboarding')
        ->assertForbidden();
});
