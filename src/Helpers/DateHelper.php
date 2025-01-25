<?php

namespace InovantiBank\Holydays\Helpers;

use Carbon\Carbon;
use InovantiBank\Holydays\Contracts\HolydaysRepositoryInterface;

class DateHelper
{
    protected HolydaysRepositoryInterface $repository;

    public function __construct(HolydaysRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Verifica se a data é fim de semana ou feriado.
     */
    public function isHolidayOrWeekend(Carbon $date): bool
    {
        if ($date->isWeekend()) {
            return true;
        }

        return $this->isHoliday($date);
    }

    /**
     * Verifica se a data é um feriado (buscando no banco).
     */
    public function isHoliday(Carbon $date): bool
    {
        $allHolidays = $this->repository->getHolidaysByYear($date->year);

        // Comparar se a data existe na lista
        foreach ($allHolidays as $holiday) {
            if ($holiday['date'] === $date->toDateString()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retorna o próximo dia útil a partir de uma data.
     * (Exemplo simples que avança dia-a-dia caso seja fim de semana/feriado)
     */
    public function getNextBusinessDay(Carbon $date): Carbon
    {
        $attempts = 0;
        $newDate = $date->copy()->addDay(); // começa a checar o "próximo" dia

        while ($this->isHolidayOrWeekend($newDate)) {
            if (++$attempts > 50) {
                // Evitar loop infinito em caso de configuração de feriados "infinita"
                break;
            }
            $newDate->addDay();
        }

        return $newDate;
    }
}
