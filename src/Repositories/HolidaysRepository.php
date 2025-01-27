<?php

namespace InovantiBank\Holidays\Repositories;

use Carbon\Carbon;
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
            if (is_array($value)) {
                [$operator, $operand] = $value;
                $query->where($field, $operator, $operand);
            } else {
                $query->where($field, '=', $value);
            }
        }

        return $query->paginate($perPage);
    }

    public function findDuplicateHoliday(array $data, ?int $excludeId = null)
    {
        $query = Holiday::query();

        if (isset($data['date'])) {
            $query->where('date', Carbon::parse($data['date']));
        }
        if (isset($data['type'])) {
            $query->where('type', $data['type']);
        }
        if (isset($data['scope'])) {
            $query->where('scope', $data['scope']);
        }
        if (isset($data['optional'])) {
            $query->where('optional', $data['optional']);
        }
        if (isset($data['state'])) {
            $query->where('state', $data['state']);
        }

        if (! is_null($excludeId)) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->first();
    }
}
