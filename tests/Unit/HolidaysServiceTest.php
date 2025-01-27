<?php

namespace Tests\Unit;

use InovantiBank\Holidays\Helpers\MultiYearHolidayCreator;
use InovantiBank\Holidays\Repositories\HolidaysRepository;
use InovantiBank\Holidays\Services\HolidaysService;
use Tests\TestCase;

class HolidaysServiceTest extends TestCase
{
    public function test_it_can_create_holiday_via_service()
    {
        $repository = new HolidaysRepository;
        $multiYearCreator = new MultiYearHolidayCreator($repository);
        $service = new HolidaysService($repository, $multiYearCreator);

        $holiday = $service->create([
            'name' => 'Outro Feriado',
            'date' => '2025-01-02 00:00:00',
            'type' => 'fix',
            'scope' => 'state',
            'optional' => true,
            'state' => 'SP',
        ], false);

        $this->assertEquals('Outro Feriado', $holiday->name);

        $this->assertDatabaseHas('holidays', [
            'name' => 'Outro Feriado',
            'date' => '2025-01-02 00:00:00',
        ]);
    }
}
