<?php

use App\Http\Enum\V1\UserTypeEnum;
use App\Models\App;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('returns null for roles on Apps I am not associated with', function () {
    $owner = User::factory()->create();
    $app = App::factory()
        ->owner($owner)
        ->create();

    $this->assertNull($this->user->roleOnApp($app));
});

it('returns the correct role for owners', function () {
    $app = App::factory()
        ->owner($this->user)
        ->create();

    $this->assertSame(UserTypeEnum::Owner, $this->user->roleOnApp($app));
});

it('returns the correct role for admins', function () {
    $owner = User::factory()->create();

    $app = App::factory()
        ->owner($owner)
        ->admin($this->user)
        ->create();

    $this->assertSame(UserTypeEnum::Admin, $this->user->roleOnApp($app));
});

it('returns the correct role for users', function () {
    $owner = User::factory()->create();

    $app = App::factory()
        ->owner($owner)
        ->user($this->user)
        ->create();

    $this->assertSame(UserTypeEnum::User, $this->user->roleOnApp($app));
});
