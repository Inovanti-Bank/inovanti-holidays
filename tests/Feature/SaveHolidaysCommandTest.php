<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use InovantiBank\Holidays\Helpers\MultiYearHolidayCreator;
use InovantiBank\Holidays\Repositories\HolidaysRepository;
use InovantiBank\Holidays\Services\HolidaysService;
use Tests\TestCase;

class SaveHolidaysCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $repository = new HolidaysRepository;
        $multiYearCreator = new MultiYearHolidayCreator($repository);
        $this->service = new HolidaysService($repository, $multiYearCreator);
    }

    public function test_save_holidays_command_with_truncate_and_default_years()
    {
        $this->service->create([
            'name' => 'Feriado Feature',
            'date' => '2025-01-01 00:00:00',
            'type' => 'fix',
            'scope' => 'national',
            'optional' => false,
        ]);

        $this->service->create([
            'name' => 'Feriado Feature',
            'date' => '2025-01-02 00:00:00',
            'type' => 'fix',
            'scope' => 'national',
            'optional' => false,
        ]);

        $this->service->create([
            'name' => 'Feriado Feature',
            'date' => '2025-01-03 00:00:00',
            'type' => 'fix',
            'scope' => 'national',
            'optional' => false,
        ]);

        $this->artisan('holidays:save', [
            '--truncate' => true,
        ])
            ->expectsConfirmation(
                'Você deseja realmente gerar feriados para 10 anos (padrão)?',
                'yes'
            )
            ->expectsConfirmation(
                "Você tem certeza que deseja limpar COMPLETAMENTE a tabela 'holidays' antes de inserir?",
                'yes'
            )

            ->expectsOutput("Tabela 'holidays' foi limpa com sucesso.")
            ->assertExitCode(0);
    }

    public function test_save_holidays_command_rejects_truncate_and_uses_ask_for_years()
    {
        $this->service->create([
            'name' => 'Feriado Antigo',
            'date' => '2025-01-01 00:00:00',
            'type' => 'fix',
            'scope' => 'national',
            'optional' => false,
        ]);

        $this->artisan('holidays:save', [
            '--truncate' => true,
        ])
            ->expectsConfirmation(
                'Você deseja realmente gerar feriados para 10 anos (padrão)?',
                'no'
            )
            ->expectsQuestion('Quantos anos você deseja gerar?', 5)
            ->expectsConfirmation(
                "Você tem certeza que deseja limpar COMPLETAMENTE a tabela 'holidays' antes de inserir?",
                'no'
            )
            ->assertExitCode(0);

        $this->assertDatabaseHas('holidays', ['name' => 'Feriado Antigo']);
    }

    public function test_save_holidays_command_with_year_from()
    {
        $this->artisan('holidays:save', [
            '--yearFrom' => 2025,
            '--years' => 2,
        ])

            ->assertExitCode(0);
    }
}
