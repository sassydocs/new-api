<?php

use App\Http\Controllers\V1\Apps\AppController;
use App\Http\Controllers\V1\Apps\AppPrivateKeyController;
use App\Http\Controllers\V1\Auth\CurrentUserController;
use App\Http\Controllers\V1\Auth\GoogleLoginController;
use App\Http\Controllers\V1\Auth\LoginController;
use App\Http\Controllers\V1\Auth\LogoutController;
use App\Http\Controllers\V1\Auth\OnboardingController;
use App\Http\Controllers\V1\Documents\DocumentCategoryController;
use App\Http\Controllers\V1\Documents\DocumentsController;
use App\Http\Controllers\V1\Documents\DocumentsVersionController;
use App\Http\Middleware\GuestsOnly;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth', 'middleware' => 'throttle:onboarding'], function() {
    Route::post('/onboarding', OnboardingController::class)->middleware(GuestsOnly::class);
    Route::post('/', LoginController::class)->middleware(GuestsOnly::class);
    Route::delete('/', LogoutController::class)->middleware('auth:sanctum');
    Route::get('/', CurrentUserController::class)->middleware('auth:sanctum');

    //Google cloud login
    Route::group(['prefix' => 'gc', 'middleware' => GuestsOnly::class], function() {
        Route::get('/redirect', [GoogleLoginController::class, 'redirect'])
            ->name('google-login.redirect');

        Route::get('/callback', [GoogleLoginController::class, 'authenticate'])
            ->name('google-login.authenticate');
    });
});

Route::group(['middleware' => ['auth:sanctum', 'throttle:api']], function() {
    Route::apiResource('apps', AppController::class)
        ->only(['store', 'destroy', 'update']);

    Route::get('apps/{app}/private-key', [AppPrivateKeyController::class, 'reveal'])
        ->name('apps.key.reveal');
    Route::post('apps/{app}/private-key', [AppPrivateKeyController::class, 'cycle'])
        ->name('apps.key.cycle');

    Route::get('categories', DocumentCategoryController::class)
        ->name('document.categories.index');

    Route::apiResource('apps/{app}/documents', DocumentsController::class)->scoped();
    Route::get('apps/{app}/documents/{document}/versions', [DocumentsVersionController::class, 'index'])
        ->name('documents.versions');
    Route::get('apps/{app}/documents/{document}/versions/diff/{version}', [DocumentsVersionController::class, 'diff'])
        ->name('documents.diff');
    Route::post('apps/{app}/documents/{document}/versions/revert/{version}', [DocumentsVersionController::class, 'revert'])
        ->name('documents.revert');
});
