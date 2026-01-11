<?php

namespace DevSarfo\LaraPrunable\Providers;

use DevSarfo\LaraPrunable\Console\Commands\PruneCommand;
use Illuminate\Support\ServiceProvider;

class PrunableProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->extend(\Illuminate\Database\Console\PruneCommand::class, fn () => new PruneCommand());
    }

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([PruneCommand::class]);
        }
    }
}
