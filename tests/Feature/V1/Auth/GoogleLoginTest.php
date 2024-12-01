<?php

use App\Mail\UserCreated;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\getJson;
use function PHPUnit\Framework\assertSame;

it('Requires a guest', function () {
    actingAs(User::factory()->create())
        ->get('/auth/gc/callback')
        ->assertForbidden();
});

it('redirects to Google', function() {
    $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');

    $abstractUser
        ->shouldReceive('getId')
        ->andReturn(rand())
        ->shouldReceive('getName')
        ->andReturn('Test User')
        ->shouldReceive('getEmail')
        ->andReturn('tester@gmail.com')
        ->shouldReceive('getAvatar')
        ->andReturn('https://en.gravatar.com/userimage');

    Socialite::shouldReceive('driver->redirect')
        ->andReturn(new RedirectResponse('/auth/gc/callback'));

    getJson('/auth/gc/redirect')
        ->assertStatus(302);
});

it('I can signup via Google', function () {
    Mail::fake();

    Carbon::setTestNow(Carbon::now());

    $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');

    $abstractUser
        ->shouldReceive('getId')
        ->andReturn(rand())
        ->shouldReceive('getName')
        ->andReturn('Test User')
        ->shouldReceive('getEmail')
        ->andReturn('tester@gmail.com')
        ->shouldReceive('getAvatar')
        ->andReturn('https://en.gravatar.com/userimage');

    Socialite::shouldReceive('driver->user')->andReturn($abstractUser);

    getJson('/auth/gc/callback')
        ->assertRedirect('/dashboard');

    assertDatabaseHas('users', [
        'name' => 'Test User',
        'email' => 'tester@gmail.com',
        'last_login_at' => Carbon::now(),
    ]);

    Mail::assertQueued(UserCreated::class);
});

it('I can login via Google', function () {
    $user = User::factory()->create();

    Carbon::setTestNow(Carbon::now());

    $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');

    $abstractUser
        ->shouldReceive('getId')
        ->andReturn('123')
        ->shouldReceive('getName')
        ->andReturn($user->name)
        ->shouldReceive('getEmail')
        ->andReturn($user->email)
        ->shouldReceive('getAvatar')
        ->andReturn('https://en.gravatar.com/userimage');

    Socialite::shouldReceive('driver->user')->andReturn($abstractUser);

    getJson('/auth/gc/callback')
        ->assertRedirect('/dashboard');

    $user->refresh();

    assertSame('123', $user->google_id);
    assertSame(Carbon::now()->toString(), $user->last_login_at->toString());
});

it('It handles invalid responses from Google', function () {
    $user = User::factory()->create();

    Carbon::setTestNow(Carbon::now());

    $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');

    $abstractUser
        ->shouldReceive('getId')
        ->andReturn('123')
        ->shouldReceive('getName')
        ->andReturn(null)
        ->shouldReceive('getEmail')
        ->andReturn('invalid')
        ->shouldReceive('getAvatar')
        ->andReturn('https://en.gravatar.com/userimage');

    Socialite::shouldReceive('driver->user')->andReturn($abstractUser);

    getJson('/auth/gc/callback')
        ->assertStatus(302);
});
