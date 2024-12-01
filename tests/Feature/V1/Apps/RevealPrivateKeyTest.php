<?php

use App\Models\App;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->admin = User::factory()->create();
    $this->owner = User::factory()->create();

    $this->application = App::factory()
        ->owner($this->owner)
        ->admin($this->admin)
        ->user($this->user)
        ->create();
});

it('requires login', function () {
    getJson(route('apps.key.reveal', $this->application))
        ->assertUnauthorized();
});

it('does not reveal private keys for users not assigned to the app', function () {
    actingAs(User::factory()->create())
        ->getJson(route('apps.key.reveal', $this->application))
        ->assertForbidden();
});

it('does not reveal private keys for users', function () {
    actingAs($this->user)
        ->getJson(route('apps.key.reveal', $this->application))
        ->assertForbidden();
});

it('can reveal an apps private key as an admin user', function () {
    actingAs($this->admin)
        ->getJson(route('apps.key.reveal', $this->application))
        ->assertSuccessful()
        ->assertJsonStructure([
            'app' => [
                'key',
            ]
        ])
        ->assertJsonFragment([
            'key' => $this->application->private_key
        ]);
});

it('can reveal an apps private key as an owner', function () {
    actingAs($this->owner)
        ->getJson(route('apps.key.reveal', $this->application))
        ->assertSuccessful()
        ->assertJsonStructure([
            'app' => [
                'key',
            ]
        ])
        ->assertJsonFragment([
            'key' => $this->application->private_key
        ]);
});
