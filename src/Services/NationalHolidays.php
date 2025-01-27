<?php

namespace InovantiBank\Holidays\Services;

use Carbon\Carbon;
use InovantiBank\Holidays\Models\Holiday;

class NationalHolidays
{
    /**
     * Salva feriados nacionais e estaduais no banco para o ano atual + N anos.
     *
     * @param  int  $yearsAhead  quantidade de anos à frente (padrão: 10)
     */
    public function saveHolidaysToDatabase(int $years, int $yearFrom, bool $truncate = false): void
    {
        $yearFrom = $yearFrom ?? Carbon::now()->year;

        for ($year = $yearFrom; $year < $yearFrom + $years; $year++) {
            $fixedHolidays = $this->getFixedHolidays($year);
            $movableHolidays = $this->getMovableHolidays($year);
            $allHolidays = array_merge($fixedHolidays, $movableHolidays);
            $allHolidays = $this->sortByDate($allHolidays);

            foreach ($allHolidays as $holiday) {
                Holiday::updateOrCreate(
                    [
                        'name' => $holiday['name'],
                        'date' => $holiday['date'],
                    ],
                    [
                        'type' => $holiday['type'],
                        'scope' => $holiday['scope'],
                        'optional' => $holiday['optional'],
                        'state' => $holiday['state'],
                    ]
                );
            }
        }
    }

    /**
     * Exemplo simples que retorna a lista de feriados fixos + móveis de um único ano (sem salvar no banco).
     */
    public function getAllHolidays(int $year): array
    {
        $fixed = $this->getFixedHolidays($year);
        $movable = $this->getMovableHolidays($year);
        $merged = array_merge($fixed, $movable);

        return $this->sortByDate($merged);
    }

    /**
     * Feriados fixos (nacionais e estaduais).
     */
    private function getFixedHolidays(int $year): array
    {
        return [
            [
                'name' => 'Ano Novo',
                'date' => "$year-01-01",
                'type' => 'fix',
                'scope' => 'national',
                'optional' => false,
                'state' => null,
            ],
            [
                'name' => 'Tiradentes',
                'date' => "$year-04-21",
                'type' => 'fix',
                'scope' => 'national',
                'optional' => false,
                'state' => null,
            ],
            [
                'name' => 'Dia do Trabalho',
                'date' => "$year-05-01",
                'type' => 'fix',
                'scope' => 'national',
                'optional' => false,
                'state' => null,
            ],
            [
                'name' => 'Independência do Brasil',
                'date' => "$year-09-07",
                'type' => 'fix',
                'scope' => 'national',
                'optional' => false,
                'state' => null,
            ],
            [
                'name' => 'Nossa Senhora Aparecida',
                'date' => "$year-10-12",
                'type' => 'fix',
                'scope' => 'national',
                'optional' => false,
                'state' => null,
            ],
            [
                'name' => 'Finados',
                'date' => "$year-11-02",
                'type' => 'fix',
                'scope' => 'national',
                'optional' => false,
                'state' => null,
            ],
            [
                'name' => 'Proclamação da República',
                'date' => "$year-11-15",
                'type' => 'fix',
                'scope' => 'national',
                'optional' => false,
                'state' => null,
            ],
            [
                'name' => 'Consciência Negra',
                'date' => "$year-11-20",
                'type' => 'fix',
                'scope' => 'national',
                'optional' => false,
                'state' => null,
            ],
            [
                'name' => 'Natal',
                'date' => "$year-12-25",
                'type' => 'fix',
                'scope' => 'national',
                'optional' => false,
                'state' => null,
            ],

            // Exemplo: Feriado estadual de São Paulo
            [
                'name' => 'Revolução Constitucionalista',
                'date' => "$year-07-09",
                'type' => 'fix',
                'scope' => 'state',
                'optional' => false,
                'state' => 'SP',
            ],
        ];
    }

    /**
     * Feriados móveis.
     */
    private function getMovableHolidays(int $year): array
    {
        $easter = $this->calculateEaster($year);
        $carnival = $easter->copy()->subDays(47);
        $goodFriday = $easter->copy()->subDays(2);
        $corpusChristi = $easter->copy()->addDays(60);

        return [
            [
                'name' => 'Carnaval',
                'date' => $carnival->format('Y-m-d'),
                'type' => 'movable',
                'scope' => 'national',
                'optional' => true,
                'state' => null,
            ],
            [
                'name' => 'Sexta-feira Santa',
                'date' => $goodFriday->format('Y-m-d'),
                'type' => 'movable',
                'scope' => 'national',
                'optional' => false,
                'state' => null,
            ],
            [
                'name' => 'Páscoa',
                'date' => $easter->format('Y-m-d'),
                'type' => 'movable',
                'scope' => 'national',
                'optional' => false,
                'state' => null,
            ],
            [
                'name' => 'Corpus Christi',
                'date' => $corpusChristi->format('Y-m-d'),
                'type' => 'movable',
                'scope' => 'national',
                'optional' => true,
                'state' => null,
            ],
        ];
    }

    /**
     * Algoritmo para calcular a Páscoa (Gregorian).
     * Referência: https://en.wikipedia.org/wiki/Date_of_Easter
     */
    private function calculateEaster(int $year): Carbon
    {
        $a = $year % 19;
        $b = intdiv($year, 100);
        $c = $year % 100;
        $d = intdiv($b, 4);
        $e = $b % 4;
        $f = intdiv($b + 8, 25);
        $g = intdiv($b - $f + 1, 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = intdiv($c, 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = intdiv($a + 11 * $h + 22 * $l, 451);
        $month = intdiv($h + $l - 7 * $m + 114, 31);
        $day = ($h + $l - 7 * $m + 114) % 31 + 1;

        return Carbon::create($year, $month, $day);
    }

    /**
     * Ordena a lista de feriados pela data (asc).
     */
    private function sortByDate(array $holidays): array
    {
        usort($holidays, function ($a, $b) {
            return strcmp($a['date'], $b['date']);
        });

        return $holidays;
    }
}
