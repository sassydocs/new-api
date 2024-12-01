<?php

use App\Models\Category;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

it('requires login', function () {
    getJson(route('document.categories.index'))
        ->assertUnauthorized();
});

it('returns all categories', function () {
    Category::factory(3)->create();

    actingAs(User::factory()->create())
        ->getJson(route('document.categories.index'))
        ->assertSuccessful()
        ->assertJsonCount(3, 'categories')
        ->assertJsonStructure([
            'categories' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                ],
            ]
        ]);
});
