<?php

namespace App\Providers;

use App\Models\App;
use App\Policies\V1\AppPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::shouldBeStrict();

        Cashier::useCustomerModel(App::class);

        Gate::policy(App::class, AppPolicy::class);

        RateLimiter::for('onboarding', function (Request $request) {
            return [
                Limit::perMinute(5)->by($request->input('ip')),
            ];
        });

        RateLimiter::for('api', function (Request $request) {
            return [
                Limit::perMinute(500)->by($request->user()->id),
            ];
        });
    }
}
