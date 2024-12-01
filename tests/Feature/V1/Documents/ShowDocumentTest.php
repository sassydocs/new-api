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
    $this->document = Document::factory()
        ->for($this->application)
        ->create();
});

it('requires login', function () {
    getJson(route('documents.show', [$this->application, $this->document]))
        ->assertUnauthorized();
});

it('blocks users not associated with the app', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->getJson(route('documents.show', [$this->application, $this->document]))
        ->assertForbidden();
});

it('must belong to the app', function () {
    $anotherAppsDocument = Document::factory()
        ->for(App::factory())
        ->create();

    actingAs($this->user)
        ->getJson(route('documents.show', [$this->application, $anotherAppsDocument]))
        ->assertNotFound();
});

it('returns the document', function () {

    actingAs($this->user)
        ->getJson(route('documents.show', [$this->application, $this->document]))
        ->assertSuccessful()
        ->assertJsonStructure([
            'document' => [
                'id',
                'title',
                'description',
            ]
        ]);
});
