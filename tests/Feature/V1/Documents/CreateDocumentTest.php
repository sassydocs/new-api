<?php

use App\Models\App;
use App\Models\Document;
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
        ->create([
            'document_limit' => 1
        ]);
    $this->document = Document::factory()->make();
});

it('requires login', function () {
    postJson(route('documents.store', [$this->application]))
        ->assertUnauthorized();
});

it('blocks users not associated with the app', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->postJson(route('documents.store', [$this->application]), $this->document->toArray())
        ->assertForbidden();
});

it('cannot exceed the maximum document allowance', function () {
    Document::factory()
        ->for($this->application)
        ->create();

    actingAs($this->user)
        ->postJson(route('documents.store', [$this->application]), $this->document->toArray())
        ->assertUnprocessable()
        ->assertJsonValidationErrors('general');
});

it('creates a versioned document without content', function () {

    $response = actingAs($this->user)
        ->postJson(route('documents.store', [$this->application]), $this->document->toArray())
        ->assertSuccessful()
        ->assertJsonStructure([
            'document' => [
                'id',
                'title',
                'description',
                'content',
            ]
        ]);

    $document = $response->json('document');

    $this->assertDatabaseHas('versions', [
        'versionable_type' => Document::class,
        'versionable_id' => $document['id'],
    ]);
});

it('creates a versioned document with content', function () {

    $document = Document::factory()
        ->content()
        ->make();

    $response = actingAs($this->user)
        ->postJson(route('documents.store', [$this->application]), $document->toArray())
        ->assertSuccessful()
        ->assertJsonStructure([
            'document' => [
                'id',
                'title',
                'description',
                'content',
            ]
        ]);

    $document = $response->json('document');

    $this->assertDatabaseHas('versions', [
        'versionable_type' => Document::class,
        'versionable_id' => $document['id'],
    ]);
});
