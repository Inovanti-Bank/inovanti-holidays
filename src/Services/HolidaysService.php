<?php

namespace InovantiBank\Holidays\Services;

use InovantiBank\Holidays\Contracts\HolidaysRepositoryInterface;

class HolidaysService
{
    protected HolidaysRepositoryInterface $repository;

    public function __construct(HolidaysRepositoryInterface $repository)
    {
        $this->repository = $repository;
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
    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    /**
     * Atualiza um feriado existente.
     */
    public function update(array $data, int $id)
    {
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
