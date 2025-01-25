<?php

namespace Tests\Unit;

use InovantiBank\Holydays\Repositories\HolydaysRepository;
use InovantiBank\Holydays\Services\HolydaysService;
use Tests\TestCase;

class HolydaysServiceTest extends TestCase
{
    public function test_it_can_create_holiday_via_service()
    {
        $service = new HolydaysService(new HolydaysRepository);
        $holiday = $service->create([
            'name' => 'Outro Feriado',
            'date' => '2025-01-02 00:00:00',
            'type' => 'fix',
            'scope' => 'state',
            'optional' => true,
            'state' => 'SP',
        ]);

        $this->assertEquals('Outro Feriado', $holiday->name);
        $this->assertDatabaseHas('holidays', [
            'name' => 'Outro Feriado',
            'date' => '2025-01-02 00:00:00',
        ]);
    }
}
