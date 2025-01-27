<?php

namespace InovantiBank\Holidays\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use InovantiBank\Holidays\Services\NationalHolidays;

class SaveHolidaysCommand extends Command
{
    protected $signature = '
        holidays:save
        {--years=10 : Quantidade de anos à frente para gerar os feriados}
        {--yearFrom= : Define a partir de qual ano devem começar os feriados}
        {--truncate : Limpa (TRUNCATE) a tabela holidays antes de inserir}
    ';

    protected $description = 'Gera e salva feriados nacionais e estaduais.';

    public function handle()
    {
        $years = (int) $this->option('years');
        $yearFrom = $this->option('yearFrom');
        $truncate = (bool) $this->option('truncate');

        if ($years === 10) {
            $confirmed = $this->confirm(
                "Você deseja realmente gerar feriados para {$years} anos (padrão)?",
                true
            );

            if (! $confirmed) {
                $years = (int) $this->ask('Quantos anos você deseja gerar?', 5);
            }
        }

        if (empty($yearFrom)) {
            $yearFrom = now()->year;
        } else {
            $yearFrom = (int) $yearFrom;
        }

        if ($truncate) {
            $confirmTruncate = $this->confirm(
                "Você tem certeza que deseja limpar COMPLETAMENTE a tabela 'holidays' antes de inserir?"
            );

            if ($confirmTruncate) {
                DB::table('holidays')->truncate();
                $this->info("Tabela 'holidays' foi limpa com sucesso.");
            } else {
                $this->info('Ok, não faremos TRUNCATE. Feriados já existentes não serão recriados.');
                $truncate = false;
            }
        }

        /** @var NationalHolidays $holidaysGenerator */
        $holidaysGenerator = app(NationalHolidays::class);

        $holidaysGenerator->saveHolidaysToDatabase($years, $yearFrom, $truncate);

        $this->info("Feriados foram salvos do ano {$yearFrom} até +{$years} anos à frente (aprox. até ".($yearFrom + $years).').');
    }
}
