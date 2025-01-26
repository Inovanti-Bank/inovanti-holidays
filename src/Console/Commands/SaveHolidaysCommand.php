<?php

namespace InovantiBank\Holidays\Console\Commands;

use Illuminate\Console\Command;
use InovantiBank\Holidays\Services\NationalHolidays;

class SaveHolidaysCommand extends Command
{
    protected $signature = 'holidays:save 
        {--years=10 : Quantidade de anos à frente para gerar os feriados}
    ';

    protected $description = 'Gera e salva feriados nacionais e estaduais para o ano atual e +N anos.';

    public function handle()
    {
        $years = (int) $this->option('years');

        // Classe que gera e salva os feriados (padrão de 10 anos)
        $holidaysGenerator = app(NationalHolidays::class);
        $holidaysGenerator->saveHolidaysToDatabase($years);

        $this->info("Feriados foram salvos até {$years} anos à frente.");
    }
}
