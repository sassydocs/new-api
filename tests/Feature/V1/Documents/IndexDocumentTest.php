<?php

use App\Models\App;
use App\Models\Document;
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
    getJson(route('documents.index', $this->application))
        ->assertUnauthorized();
});

it('returns all documents', function () {
    Document::factory(3)
        ->for($this->application, 'app')
        ->create();

    actingAs($this->user)
        ->getJson(route('documents.index', $this->application))
        ->assertSuccessful()
        ->assertJsonCount(3, 'documents')
        ->assertJsonStructure([
            'documents' => [
                '*' => [
                    'id',
                    'title',
                    'description',
                ],
            ]
        ]);
});

it('returns paginated results', function () {
    Document::factory(15)
        ->for($this->application, 'app')
        ->create();

    actingAs($this->user)
        ->getJson(route('documents.index', $this->application))
        ->assertSuccessful()
        ->assertJsonCount(10, 'documents')
        ->assertJsonStructure([
            'documents' => [
                '*' => [
                    'id',
                    'title',
                    'description',
                ],
            ]
        ]);
});


it('returns results filtered by name', function () {
    foreach(['a','b','c'] as $name) {
        Document::factory()
            ->for($this->application, 'app')
            ->create([
                'title' => $name
            ]);
    }

    actingAs($this->user)
        ->getJson(route('documents.index', [ $this->application,
            'filter[title]' => 'b',
        ]))
        ->assertSuccessful()
        ->assertJsonCount(1, 'documents');
});

it('handles incorrectly formatted filters', function () {
    actingAs($this->user)
        ->getJson(route('documents.index', [ $this->application,
            'filter[bla]' => 'b',
        ]))
        ->assertStatus(403);
});
