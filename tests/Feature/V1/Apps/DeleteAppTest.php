<?php

use App\Models\App;
use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\deleteJson;

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
    deleteJson(route('apps.destroy', $this->application))
        ->assertUnauthorized();
});

it('can not be deleted by an unknown user', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->deleteJson(route('apps.destroy', $this->application))
        ->assertForbidden();
});

it('can not be deleted by an admin', function () {
    actingAs($this->admin)
        ->deleteJson(route('apps.destroy', $this->application))
        ->assertForbidden();
});

it('can be deleted by the owner', function () {
    actingAs($this->owner)
        ->deleteJson(route('apps.destroy', $this->application))
        ->assertSuccessful();

    $this->application->refresh();

    $this->assertNotNull($this->application->deleted_at);
});
