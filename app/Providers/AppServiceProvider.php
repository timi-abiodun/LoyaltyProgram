<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Strategies\AmountSpentStrategy;
use App\Strategies\PurchaseCountStrategy;
use App\Services\ProcessAchievementsService;
use App\Services\AchievementService;

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
        //
    }
}
