<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use InovantiBank\Holidays\Contracts\HolidaysRepositoryInterface;
use InovantiBank\Holidays\Helpers\MultiYearHolidayCreator;
use InovantiBank\Holidays\Repositories\HolidaysRepository;
use InovantiBank\Holidays\Services\HolidaysService;
use RuntimeException;
use Tests\TestCase;

class HolidaysTest extends TestCase
{
    use RefreshDatabase;

    protected HolidaysService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $repository = new HolidaysRepository;
        $multiYearCreator = new MultiYearHolidayCreator($repository);
        $this->service = new HolidaysService($repository, $multiYearCreator);
    }

    public function test_it_can_create_holiday()
    {
        $holiday = $this->service->create([
            'name' => 'Feriado Feature',
            'date' => '2025-01-01 00:00:00',
            'type' => 'fix',
            'scope' => 'national',
            'optional' => false,
        ]);

        $this->assertNotNull($holiday['created'][0]->id);
        $this->assertEquals('Feriado Feature', $holiday['created'][0]->name);

        $this->assertDatabaseHas('holidays', [
            'name' => 'Feriado Feature',
            'date' => '2025-01-01 00:00:00',
        ]);
    }

    public function test_it_can_create_holiday_for_all_years()
    {
        $this->service->create([
            'name' => 'Feriado Existente 2025',
            'date' => '2025-05-01',
            'type' => 'fix',
            'scope' => 'state',
            'optional' => false,
            'state' => 'SP',
        ], false);

        $this->service->create([
            'name' => 'Feriado Existente 2026',
            'date' => '2026-05-01',
            'type' => 'fix',
            'scope' => 'state',
            'optional' => false,
            'state' => 'SP',
        ], false);

        $result = $this->service->create([
            'name' => 'Feriado Multi',
            'date' => '2025-10-15',
            'type' => 'fix',
            'scope' => 'state',
            'optional' => false,
            'state' => 'SP',
        ], true);

        $this->assertIsArray($result);
        $this->assertEquals(2, $result['created_count']);
        $this->assertEquals(0, $result['skipped_count']);

        $this->assertDatabaseHas('holidays', [
            'name' => 'Feriado Multi',
            'date' => '2025-10-15 00:00:00',
        ]);
        $this->assertDatabaseHas('holidays', [
            'name' => 'Feriado Multi',
            'date' => '2026-10-15 00:00:00',
        ]);
    }

    public function test_it_skips_duplicate_holiday_in_multi_year_creation()
    {
        $this->service->create([
            'name' => 'Feriado Existente',
            'date' => '2025-10-15',
            'type' => 'fix',
            'scope' => 'state',
            'optional' => false,
            'state' => 'SP',
        ], false);

        $this->service->create([
            'name' => 'Feriado Outro 2026',
            'date' => '2026-01-01',
            'type' => 'fix',
            'scope' => 'state',
            'optional' => false,
            'state' => 'SP',
        ], false);

        $result = $this->service->create([
            'name' => 'Feriado Multi',
            'date' => '2025-10-15',
            'type' => 'fix',
            'scope' => 'state',
            'optional' => false,
            'state' => 'SP',
        ], true);

        $this->assertIsArray($result);
        $this->assertEquals(1, $result['created_count']);
        $this->assertEquals(1, $result['skipped_count']);

        $this->assertDatabaseHas('holidays', [
            'name' => 'Feriado Multi',
            'date' => '2026-10-15 00:00:00',
        ]);
        $this->assertDatabaseMissing('holidays', [
            'name' => 'Feriado Multi',
            'date' => '2025-10-15 00:00:00',
        ]);
    }

    public function test_it_throws_exception_on_duplicate_single_creation()
    {
        $this->service->create([
            'name' => 'Feriado Duplicado',
            'date' => '2025-05-10',
            'type' => 'fix',
            'scope' => 'state',
            'optional' => false,
            'state' => 'SP',
        ], false);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Já existe um feriado com esses parâmetros.');

        $this->service->create([
            'name' => 'Feriado Duplicado',
            'date' => '2025-05-10',
            'type' => 'fix',
            'scope' => 'state',
            'optional' => false,
            'state' => 'SP',
        ], false);
    }

    public function test_it_can_list_holidays()
    {
        $this->service->create([
            'name' => 'Feriado 1',
            'date' => '2025-02-01',
            'type' => 'fix',
            'scope' => 'national',
            'optional' => false,
        ]);

        $this->service->create([
            'name' => 'Feriado 2',
            'date' => '2025-03-01',
            'type' => 'fix',
            'scope' => 'national',
            'optional' => false,
        ]);

        $paginated = $this->service->list($perPage = 10);

        $this->assertCount(2, $paginated->items());
        $this->assertEquals(2, $paginated->total());
    }

    public function test_it_can_update_holiday()
    {
        $holiday = $this->service->create([
            'name' => 'Feriado Velho',
            'date' => '2025-02-01',
            'type' => 'fix',
            'scope' => 'national',
            'optional' => false,
        ]);

        $updated = $this->service->update([
            'name' => 'Feriado Novo',
        ], $holiday['created'][0]->id);

        $this->assertEquals('Feriado Novo', $updated->name);
        $this->assertDatabaseHas('holidays', [
            'id' => $holiday['created'][0]->id,
            'name' => 'Feriado Novo',
        ]);
        $this->assertDatabaseMissing('holidays', [
            'id' => $holiday['created'][0]->id,
            'name' => 'Feriado Velho',
        ]);
    }

    public function test_it_throws_exception_on_update_when_data_conflicts_with_another_holiday()
    {
        $holidayA = $this->service->create([
            'name' => 'Feriado A',
            'date' => '2025-07-01',
            'type' => 'fix',
            'scope' => 'state',
            'optional' => false,
            'state' => 'SP',
        ], false);

        $holidayB = $this->service->create([
            'name' => 'Feriado B',
            'date' => '2025-07-02',
            'type' => 'fix',
            'scope' => 'state',
            'optional' => false,
            'state' => 'SP',
        ], false);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Já existe outro feriado com esses parâmetros.');

        $this->service->update([
            'name' => 'Feriado B (duplicado)',
            'date' => '2025-07-01',
            'type' => 'fix',
            'scope' => 'state',
            'optional' => false,
            'state' => 'SP',
        ], $holidayB->id);
    }

    public function test_it_can_delete_holiday()
    {
        $holiday = $this->service->create([
            'name' => 'Feriado a Deletar',
            'date' => '2025-04-01',
            'type' => 'fix',
            'scope' => 'national',
            'optional' => false,
        ]);

        $deleted = $this->service->delete($holiday['created'][0]->id);

        $this->assertTrue((bool) $deleted, 'Esperado retorno true ao deletar');

        $this->assertSoftDeleted('holidays', [
            'id' => $holiday['created'][0]->id,
        ]);
    }

    public function test_it_can_filter_holidays()
    {
        $this->service->create([
            'name' => 'Dia do Teste 1',
            'date' => '2025-05-01',
            'type' => 'fix',
            'scope' => 'national',
            'optional' => false,
        ]);

        $this->service->create([
            'name' => 'Dia do Teste 2',
            'date' => '2025-06-01',
            'type' => 'fix',
            'scope' => 'state',
            'optional' => false,
            'state' => 'SP',
        ]);

        $repo = $this->app->make(HolidaysRepositoryInterface::class);

        $filtered = $repo->filter(['scope' => 'state']);
        $this->assertCount(1, $filtered);
        $this->assertEquals('Dia do Teste 2', $filtered->first()->name);
    }
}
