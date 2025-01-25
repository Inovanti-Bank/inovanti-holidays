<?php

namespace InovantiBank\Holydays;

use Illuminate\Support\ServiceProvider;
use InovantiBank\Holydays\Console\Commands\SaveHolidaysCommand;
use InovantiBank\Holydays\Contracts\HolydaysRepositoryInterface;
use InovantiBank\Holydays\Repositories\HolydaysRepository;

class HolydaysServiceProvider extends ServiceProvider
{
    /**
     * Registra bindings no container.
     */
    public function register()
    {
        // Vincular a interface ao repositório concreto
        $this->app->bind(HolydaysRepositoryInterface::class, HolydaysRepository::class);
    }

    /**
     * Faz o boot das funcionalidades do pacote.
     */
    public function boot()
    {
        // Publicar migrações (caso o usuário queira rodar "php artisan vendor:publish")
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'inovanti-holydays-migrations');

        // Registrar comando customizado
        if ($this->app->runningInConsole()) {
            $this->commands([
                SaveHolidaysCommand::class,
            ]);
        }
    }
}
