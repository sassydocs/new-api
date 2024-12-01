<?php

use App\Models\User;
use Illuminate\Support\Str;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->password = Str::random(12);
    $this->user = User::factory()->create([
        'password' => $this->password,
    ]);
});

it('blocks users who are already logged in', function () {
    actingAs($this->user)
        ->postJson('/auth')
        ->assertForbidden();
});

it('requires email address', function () {
    postJson('/auth', [
        'password' => 'invalid',
    ])
        ->assertUnprocessable();
});

it('requires password', function () {
    postJson('/auth', [
        'email' => $this->user->email,
    ])
        ->assertUnprocessable();
});

it('requires valid credentials', function () {
    postJson('/auth', [
        'email' => $this->user->email,
        'password' => 'invalid',
    ])
        ->assertUnprocessable()
        ->assertJson([
            'message' => 'Check your email address and password',
        ]);
});

it('logs me in', function () {
    postJson('/auth', [
        'email' => $this->user->email,
        'password' => $this->password,
    ])
        ->assertSuccessful();
});
