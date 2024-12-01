<?php

use App\Models\App;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

it('Requires login', function () {
    getJson('/auth')
        ->assertUnauthorized();
});

it('I can get my user details', function () {
    $user = User::factory()
        ->has(App::factory(2), 'ownedApps')
        ->create();

    $apps = $user->apps;

    actingAs($user)
        ->getJson('/auth')
        ->assertSuccessful()
        ->assertJson([
            'auth' => [
                'email' => $user->email,
                'name' => $user->name,
                'apps' => $apps->map(fn($app) => [
                    'id' => $app->id,
                    'name' => $app->name,
                    'description' => $app->description,
                    'owner' => [
                        'id' => $user->id,
                        'name' => $user->name,
                    ]
                ])->toArray(),
            ]
        ]);
});
