<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Strategies\AmountSpentStrategy;
use App\Strategies\PurchaseCountStrategy;
use App\Services\ProcessAchievementsService;
use App\Services\AchievementService;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // 1. Bind each concrete class
        $this->app->bind(AmountSpentStrategy::class);
        $this->app->bind(PurchaseCountStrategy::class);

        // 2. "Tag" them under a single name
        $this->app->tag([AmountSpentStrategy::class, PurchaseCountStrategy::class], 'achievement.strategies');

        // 3. Tell the Service how to resolve the 'strategies' array
        $this->app->bind(ProcessAchievementsService::class, function ($app) {
            return new ProcessAchievementsService($app->tagged('achievement.strategies'));
        });

        $this->app->bind(AchievementService::class, function ($app) {
            return new AchievementService($app->tagged('achievement.strategies'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Limiter for Auth *(Login/Register)
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(5)->by($request->input('email').$request->ip());
        }); 

        // Limiter for Purchases (More strict to prevent double-spend spam)
        RateLimiter::for('purchases', function (Request $request) {
            return Limit::perMinute(3)->by($request->user()?->id ?: $request->ip());
        });
    }
}
