<?php

namespace DevSarfo\LaraPrunable\Providers;

use DevSarfo\LaraPrunable\Console\Commands\LaraPruneCommand;
use Illuminate\Database\Console\PruneCommand;
use Illuminate\Support\ServiceProvider;

class LaraPrunableProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->extend(PruneCommand::class, fn () => new LaraPruneCommand);
    }

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([LaraPruneCommand::class]);
        }
    }
}
