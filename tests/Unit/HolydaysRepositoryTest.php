<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use InovantiBank\Holydays\Exceptions\InvalidYearException;
use InovantiBank\Holydays\Models\Holiday;
use InovantiBank\Holydays\Repositories\HolydaysRepository;
use Tests\TestCase;

class HolydaysRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected HolydaysRepository $repo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repo = new HolydaysRepository;
    }

    public function test_can_create_holiday()
    {
        $holiday = $this->repo->create([
            'name' => 'Feriado Teste',
            'date' => '2025-01-01',
            'type' => 'fix',
            'scope' => 'national',
            'optional' => false,
        ]);

        $this->assertNotNull($holiday->id);
        $this->assertEquals('Feriado Teste', $holiday->name);
    }

    public function test_filter_by_exact_name()
    {
        Holiday::create([
            'name' => 'Ano Novo',
            'date' => '2025-01-01',
            'type' => 'fix',
            'scope' => 'national',
            'optional' => false,
            'state' => null,
        ]);

        Holiday::create([
            'name' => 'Carnaval',
            'date' => '2025-02-25',
            'type' => 'movable',
            'scope' => 'national',
            'optional' => true,
            'state' => null,
        ]);

        $conditions = ['name' => 'Ano Novo'];
        $results = $this->repo->filter($conditions, $perPage = 10);

        $this->assertCount(1, $results);
        $this->assertEquals('Ano Novo', $results->first()->name);
    }

    public function test_filter_by_multiple_conditions()
    {
        Holiday::create([
            'name' => 'Ano Novo',
            'date' => '2026-01-01',
            'type' => 'fix',
            'scope' => 'national',
            'optional' => false,
            'state' => null,
        ]);

        Holiday::create([
            'name' => 'Ano Novo Estadual',
            'date' => '2026-01-01',
            'type' => 'fix',
            'scope' => 'state',
            'optional' => false,
            'state' => 'SP',
        ]);

        $conditions = [
            'scope' => 'state',
            'state' => 'SP',
        ];

        $results = $this->repo->filter($conditions);

        $this->assertCount(1, $results);
        $this->assertEquals('Ano Novo Estadual', $results->first()->name);
    }

    public function test_filter_by_like_operator()
    {
        Holiday::create([
            'name' => 'Carnaval 2025',
            'date' => '2025-02-25',
            'type' => 'movable',
            'scope' => 'national',
            'optional' => true,
            'state' => null,
        ]);

        Holiday::create([
            'name' => 'Carnaval 2026',
            'date' => '2026-02-14',
            'type' => 'movable',
            'scope' => 'national',
            'optional' => true,
            'state' => null,
        ]);

        Holiday::create([
            'name' => 'Páscoa 2025',
            'date' => '2025-03-30',
            'type' => 'movable',
            'scope' => 'national',
            'optional' => false,
            'state' => null,
        ]);

        $conditions = [
            'name' => ['like', '%Carnaval%'],
        ];

        $results = $this->repo->filter($conditions, 10);

        $this->assertCount(2, $results);
        $this->assertTrue(
            $results->contains(fn ($holiday) => $holiday->name === 'Carnaval 2025')
        );
        $this->assertTrue(
            $results->contains(fn ($holiday) => $holiday->name === 'Carnaval 2026')
        );
    }

    public function test_filter_returns_no_results_when_nothing_matches()
    {
        $conditions = ['name' => 'Feriado Que Não Existe'];
        $results = $this->repo->filter($conditions);

        $this->assertCount(0, $results);
    }

    public function test_get_holidays_by_year_throws_invalid_year_exception()
    {
        $this->expectException(InvalidYearException::class);
        $this->expectExceptionMessage('O ano fornecido (1800) é inválido para consulta de feriados.');

        $this->repo->getHolidaysByYear(1800);
    }
}
