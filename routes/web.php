<?php

use App\Mail\UserCreated;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $user = User::factory()->make();

    return new UserCreated($user);
});
