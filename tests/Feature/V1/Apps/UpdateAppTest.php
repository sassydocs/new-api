<?php

use App\Models\App;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\putJson;

beforeEach(function () {
    $this->owner = User::factory()->create();
    $this->user = User::factory()->create();
    $this->application = App::factory()
        ->owner($this->owner)
        ->user($this->user)
        ->create();
});

it('Requires login', function () {
    putJson(route('apps.update', $this->application))
        ->assertUnauthorized();
});

it('Requires a name', function () {
    actingAs($this->owner)
        ->putJson(route('apps.update', $this->application))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('name');
});

it('Has an optional description', function () {
    actingAs($this->owner)
        ->putJson(route('apps.update', $this->application), [
            'name' => 'Updated name',
        ])
        ->assertSuccessful();
});

it('Requires the name to be unique for each owner', function () {
    $existingApp = App::factory()
        ->owner($this->owner)
        ->create();

    actingAs($this->owner)
        ->putJson(route('apps.update', $this->application), [
            'name' => $existingApp->name,
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('name');
});

it('does not allow users to update an app', function () {
    actingAs($this->user)
        ->putJson(route('apps.update', $this->application), [
            'name' => 'Updated name',
            'description' => 'Updated description',
        ])
        ->assertForbidden();
});

it('allows owners to update an app', function () {
    actingAs($this->owner)
        ->putJson(route('apps.update', $this->application), [
            'name' => 'Updated name',
            'description' => 'Updated description',
        ])
        ->assertSuccessful()
        ->assertJsonStructure([
            'app' => [
                'id',
                'name',
                'description',
                'owner' => [
                    'id',
                    'name',
                ]
            ]
        ])
        ->assertJsonFragment([
            'name' => 'Updated name',
            'description' => 'Updated description',
            'owner' => [
                'id' => $this->owner->id,
                'name' => $this->owner->name,
            ]
        ]);
});
