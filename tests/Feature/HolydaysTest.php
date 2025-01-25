<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use InovantiBank\Holydays\Contracts\HolydaysRepositoryInterface;
use InovantiBank\Holydays\Services\HolydaysService;
use Tests\TestCase;

class HolydaysTest extends TestCase
{
    use RefreshDatabase;

    protected HolydaysService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(HolydaysService::class);
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

        $this->assertNotNull($holiday->id);
        $this->assertEquals('Feriado Feature', $holiday->name);

        $this->assertDatabaseHas('holidays', [
            'name' => 'Feriado Feature',
            'date' => '2025-01-01 00:00:00',
        ]);
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
        // Cria
        $holiday = $this->service->create([
            'name' => 'Feriado Velho',
            'date' => '2025-02-01',
            'type' => 'fix',
            'scope' => 'national',
            'optional' => false,
        ]);

        $updated = $this->service->update([
            'name' => 'Feriado Novo',
        ], $holiday->id);

        $this->assertEquals('Feriado Novo', $updated->name);
        $this->assertDatabaseHas('holidays', [
            'id' => $holiday->id,
            'name' => 'Feriado Novo',
        ]);
        $this->assertDatabaseMissing('holidays', [
            'id' => $holiday->id,
            'name' => 'Feriado Velho',
        ]);
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

        $deleted = $this->service->delete($holiday->id);

        $this->assertTrue((bool) $deleted, 'Esperado retorno true ao deletar');

        $this->assertSoftDeleted('holidays', [
            'id' => $holiday->id,
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

        $repo = $this->app->make(HolydaysRepositoryInterface::class);

        $filtered = $repo->filter(['scope' => 'state']);
        $this->assertCount(1, $filtered);
        $this->assertEquals('Dia do Teste 2', $filtered->first()->name);
    }
}
