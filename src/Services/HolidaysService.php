<?php

namespace InovantiBank\Holidays\Services;

use InovantiBank\Holidays\Contracts\HolidaysRepositoryInterface;
use InovantiBank\Holidays\Helpers\MultiYearHolidayCreator;
use RuntimeException;

class HolidaysService
{
    protected HolidaysRepositoryInterface $repository;

    protected MultiYearHolidayCreator $multiYearCreator;

    public function __construct(HolidaysRepositoryInterface $repository, MultiYearHolidayCreator $multiYearCreator)
    {
        $this->repository = $repository;
        $this->multiYearCreator = $multiYearCreator;
    }

    /**
     * Lista feriados paginados.
     */
    public function list(int $perPage = 10)
    {
        return $this->repository->list($perPage);
    }

    /**
     * Cria um novo feriado.
     */
    public function create(array $data, bool $persistentForAllYears = true)
    {
        if (! $persistentForAllYears) {
            $duplicate = $this->repository->findDuplicateHoliday($data);
            if ($duplicate) {
                throw new RuntimeException('Já existe um feriado com esses parâmetros.');
            }

            return $this->repository->create($data);
        }

        return $this->multiYearCreator->createForAllYears($data);
    }

    /**
     * Atualiza um feriado existente.
     */
    public function update(array $data, int $id)
    {
        $duplicate = $this->repository->findDuplicateHoliday($data, $id);

        if ($duplicate) {
            throw new RuntimeException('Já existe outro feriado com esses parâmetros.');
        }

        return $this->repository->update($data, $id);
    }

    /**
     * Deleta um feriado.
     */
    public function delete(int $id)
    {
        return $this->repository->delete($id);
    }

    /**
     * busca um feriado com filtros
     */
    public function filterHolidays(array $conditions = [], int $perPage = 10)
    {
        return $this->repository->filter($conditions, $perPage);
    }
}
