<?php

use App\Models\App;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->user = User::factory()->create([
        'max_apps' => 2,
    ]);
});

it('Requires login', function () {
    postJson(route('apps.store'), [])
        ->assertUnauthorized();
});

it('Requires a name', function () {
    actingAs($this->user)
        ->postJson(route('apps.store'), [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('name');
});

it('Has an optional description', function () {
    actingAs($this->user)
        ->postJson(route('apps.store'), [
            'name' => 'Test name',
        ])
        ->assertSuccessful();
});

it('Requires the name to be unique for each owner', function () {
    $existingApp = App::factory()
        ->owner($this->user)
        ->create();

    actingAs($this->user)
        ->postJson(route('apps.store'), [
            'name' => $existingApp->name,
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('name');
});

it('cannot exceed my maximum app allowance', function () {
    App::factory(2)
        ->owner($this->user)
        ->create();

    actingAs($this->user)
        ->postJson(route('apps.store'), [
            'name' => 'Too many apps'
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('general');
});

it('can create an app', function () {
    $app = App::factory()->make()->only(['name', 'description']);

    actingAs($this->user)
        ->postJson(route('apps.store'), $app)
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
            'name' => $app['name'],
            'description' => $app['description'],
            'owner' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ]
        ]);
});
