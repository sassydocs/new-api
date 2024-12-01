<?php

use App\Models\App;
use App\Models\Document;
use App\Models\User;
use Carbon\Carbon;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;

beforeEach(function () {
    Carbon::setTestNow(Carbon::now());

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


it('returns a list of versions for a document', function () {
    $this->document->update([
        'title' => 'Updated title',
    ]);

    actingAs($this->user)
        ->getJson(route('documents.versions', [$this->application, $this->document]))
        ->assertSuccessful()
        ->assertJsonCount(2, 'versions')
        ->assertJsonStructure([
            'versions' => [
                '*' => [
                    'id',
                    'date',
                ]
            ]
        ]);

    $this->document->refresh();

    $this->assertSame('Updated title', $this->document->title);
});

it('reverts to a previous versions of a document', function () {
    $originalTitle = $this->document->title;

    $this->document->update([
        'title' => 'Updated title',
    ]);

    $response = actingAs($this->user)
        ->getJson(route('documents.versions', [$this->application, $this->document]))
        ->assertSuccessful()
        ->assertJsonCount(2, 'versions')
        ->assertJsonStructure([
            'versions' => [
                '*' => [
                    'id',
                    'date',
                ]
            ]
        ]);

    $originalVersionId = $response->json('versions')[0]['id'];

    // refresh the document and assert the title is updated (version 2)

    $this->document->refresh();

    $this->assertSame('Updated title', $this->document->title);

    // revert the document back to version 1

    actingAs($this->user)
        ->postJson(route('documents.revert', [$this->application, $this->document, $originalVersionId]))
        ->assertSuccessful();

    // refresh the document and assert the title is updated (version 1)

    $this->document->refresh();

    $this->assertSame($originalTitle, $this->document->title);
});
