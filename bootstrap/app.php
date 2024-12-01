<?php

use App\Exceptions\GuestsOnlyException;
use App\Http\Controllers\Controller;
use App\Http\Middleware\ForceJson;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Laravel\Socialite\Two\InvalidStateException;
use Sentry\Laravel\Integration;
use Spatie\QueryBuilder\Exceptions\InvalidFilterQuery;
use Spatie\QueryBuilder\Exceptions\InvalidIncludeQuery;
use Spatie\QueryBuilder\Exceptions\InvalidSortQuery;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: '/',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(ForceJson::class);
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->reportable(function (Throwable $e) {
            if (app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }
        });

        $controller = new Controller();

        $exceptions->renderable(function (MethodNotAllowedHttpException $e) use ($controller) {
            return $controller->error('Method not allowed', 405);
        });

        $exceptions->renderable(function (InvalidStateException $e) use ($controller) {
            Integration::captureUnhandledException($e);
            return $controller->error('Login failed due to an invalid state', 401);
        });

        $exceptions->renderable(function (AuthenticationException $e) use ($controller) {
            return $controller->error('You must login or provide an auth token', 401);
        });

        $exceptions->renderable(function (AccessDeniedHttpException $e) use ($controller) {
            return $controller->error('You are not permitted to do that', 403);
        });

        $exceptions->renderable(function (GuestsOnlyException $e) use ($controller) {
            return $controller->error('You are already logged in', 403);
        });

        $exceptions->renderable(function (ThrottleRequestsException $e) use ($controller) {
            return $controller->error('You have made too many requests, please wait a moment and try again', 429);
        });

        $exceptions->renderable(function (InvalidIncludeQuery $e) use ($controller) {
            return $controller->fail([
                'message' => 'Your request contains an invalid option',
                'suggestions' => $e->allowedIncludes,
            ], 403);
        });

        $exceptions->renderable(function (InvalidSortQuery $e) use ($controller) {
            return $controller->fail([
                'message' => 'Your request contains an invalid option',
                'suggestions' => $e->allowedSorts,
            ], 403);
        });

        $exceptions->renderable(function (InvalidFilterQuery $e) use ($controller) {
            return $controller->fail([
                'message' => 'Your request contains an invalid option',
                'suggestions' => $e->allowedFilters,
            ], 403);
        });

        $exceptions->renderable(function (NotFoundHttpException $e) use ($controller) {
            return $controller->error('Not found', 404);
        });
    })->create();
