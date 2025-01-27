<?php

namespace InovantiBank\Holidays\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InovantiBank\Holidays\Contracts\HolidaysRepositoryInterface;
use InovantiBank\Holidays\Models\Holiday;

class MultiYearHolidayCreator
{
    protected HolidaysRepositoryInterface $repository;

    public function __construct(HolidaysRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Cria feriados para todos os anos existentes na tabela, a partir do ano do $data['date'].
     * Retorna um array com informações sobre os feriados criados e/ou pulados.
     */
    public function createForAllYears(array $data): array
    {
        $originalDate = Carbon::parse($data['date']);
        $baseYear = $originalDate->year;
        $month = $originalDate->month;
        $day = $originalDate->day;

        $selectYear = $this->getCorrectRaw();

        $distinctYears = Holiday::selectRaw("DISTINCT {$selectYear} as year")
            ->whereRaw("{$selectYear} >= ?", [$baseYear])
            ->orderBy('year')
            ->pluck('year')
            ->toArray();

        if (! in_array($baseYear, $distinctYears)) {
            $distinctYears[] = $baseYear;
        }

        sort($distinctYears);
        $distinctYears = array_unique($distinctYears);

        $created = [];
        $skipped = [];

        foreach ($distinctYears as $year) {
            $tempDate = Carbon::create($year, $month, $day)->format('Y-m-d');

            $tempData = $data;
            $tempData['date'] = $tempDate;

            $duplicate = $this->repository->findDuplicateHoliday($tempData);

            if ($duplicate) {
                $skipped[] = $year;

                continue;
            }

            $created[] = $this->repository->create($tempData);
        }

        return [
            'created_count' => count($created),
            'skipped_count' => count($skipped),
            'created' => $created,
            'skipped_years' => $skipped,
            'persistent_for_all_years' => true,
        ];
    }

    private function getCorrectRaw(): string
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            return 'EXTRACT(YEAR FROM date)';
        } else {
            return "strftime('%Y', date)";
        }
    }
}
