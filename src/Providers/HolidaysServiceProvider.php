<?php

namespace InovantiBank\Holidays\Providers;

use Illuminate\Support\ServiceProvider;
use InovantiBank\Holidays\Console\Commands\SaveHolidaysCommand;
use InovantiBank\Holidays\Contracts\HolidaysRepositoryInterface;
use InovantiBank\Holidays\Repositories\HolidaysRepository;

class HolidaysServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(HolidaysRepositoryInterface::class, HolidaysRepository::class);
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../database/migrations/' => database_path('migrations'),
        ], 'holidays-migrations');

        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                SaveHolidaysCommand::class,
            ]);
        }
    }
}
