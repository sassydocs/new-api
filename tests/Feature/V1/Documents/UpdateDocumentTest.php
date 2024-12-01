<?php

use App\Models\App;
use App\Models\Document;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

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
    $this->document = Document::factory()
        ->for($this->application)
        ->create();
});

it('requires login', function () {
    putJson(route('documents.update', [$this->application, $this->document]))
        ->assertUnauthorized();
});

it('blocks users not associated with the app', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->putJson(route('documents.update', [$this->application, $this->document]))
        ->assertForbidden();
});

it('creates a versioned document', function () {

    actingAs($this->user)
        ->putJson(route('documents.update', [$this->application, $this->document]), [
            'title' => 'Updated title',
        ])
        ->assertSuccessful()
        ->assertJsonStructure([
            'document' => [
                'id',
                'title',
                'description',
                'content',
            ]
        ]);

    $this->document->refresh();

    $this->assertCount(2, $this->document->versions);
});

it('ignores the existing title as unique', function () {

    actingAs($this->user)
        ->putJson(route('documents.update', [$this->application, $this->document]), [
            'title' => $this->document->title,
        ])
        ->assertSuccessful();
});
