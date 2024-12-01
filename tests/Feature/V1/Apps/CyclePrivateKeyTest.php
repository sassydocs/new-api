<?php

use App\Models\App;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;

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
    postJson(route('apps.key.cycle', $this->application))
        ->assertUnauthorized();
});

it('does not cycle private keys for users not assigned to the app', function () {
    actingAs(User::factory()->create())
        ->getJson(route('apps.key.cycle', $this->application))
        ->assertForbidden();
});

it('does not cycle private keys for users', function () {
    actingAs($this->user)
        ->getJson(route('apps.key.cycle', $this->application))
        ->assertForbidden();
});

it('can cycle an apps private key for admins', function () {
    $response = actingAs($this->admin)
        ->postJson(route('apps.key.cycle', $this->application))
        ->assertSuccessful()
        ->assertJsonStructure([
            'app' => [
                'key',
            ]
        ]);

    $key = $response->json('app.key');

    $this->assertNotEquals($key, $this->application->private_key);
});

it('can cycle an apps private key for owners', function () {
    $response = actingAs($this->owner)
        ->postJson(route('apps.key.cycle', $this->application))
        ->assertSuccessful()
        ->assertJsonStructure([
            'app' => [
                'key',
            ]
        ]);

    $key = $response->json('app.key');

    $this->assertNotEquals($key, $this->application->private_key);
});
