<?php

namespace InovantiBank\Holidays\Repositories;

use InovantiBank\Holidays\Contracts\HolidaysRepositoryInterface;
use InovantiBank\Holidays\Exceptions\InvalidYearException;
use InovantiBank\Holidays\Models\Holiday;

class HolidaysRepository implements HolidaysRepositoryInterface
{
    public function list(int $perPage = 10)
    {
        return Holiday::paginate($perPage);
    }

    public function create(array $data)
    {
        return Holiday::create($data);
    }

    public function update(array $data, int $id)
    {
        $holiday = Holiday::findOrFail($id);
        $holiday->update($data);

        return $holiday;
    }

    public function delete(int $id)
    {
        $holiday = Holiday::findOrFail($id);

        return $holiday->delete();
    }

    /**
     * Lança InvalidYearException se o ano não atender um critério.
     * Ajuste as regras de validação conforme necessidade.
     */
    public function getHolidaysByYear(int $year): array
    {
        $year = (int) $year;

        if ($year < 1900 || $year > 2100) {
            throw new InvalidYearException($year);
        }

        return Holiday::whereYear('date', $year)->get()->toArray();
    }

    /**
     * Permite que o usuário informe um array de condições que
     * serão convertidas em where() no Eloquent.
     */
    public function filter(array $conditions = [], int $perPage = 10)
    {
        $query = Holiday::query();

        foreach ($conditions as $field => $value) {
            /**
             * Opções de implementação:
             * 1) Se $value for um array, interpretamos como [operador, valor].
             * 2) Caso contrário, interpretamos como 'WHERE $field = $value'.
             */
            if (is_array($value)) {
                // Ex: ['name' => ['like', '%carnaval%']]
                [$operator, $operand] = $value;
                $query->where($field, $operator, $operand);
            } else {
                // Ex: ['name' => 'Ano Novo']
                $query->where($field, '=', $value);
            }
        }

        return $query->paginate($perPage);
    }
}
