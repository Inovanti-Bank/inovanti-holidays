<?php

namespace InovantiBank\Holidays\Contracts;

interface HolidaysRepositoryInterface
{
    /**
     * Lista todos os feriados paginados.
     */
    public function list(int $perPage = 10);

    /**
     * Cria um novo feriado.
     */
    public function create(array $data);

    /**
     * Atualiza um feriado existente.
     */
    public function update(array $data, int $id);

    /**
     * Deleta um feriado pelo ID.
     */
    public function delete(int $id);

    /**
     * Retorna todos os feriados de determinado ano (sem paginação).
     */
    public function getHolidaysByYear(int $year): array;

    /**
     * Retorna todos os feriados de determinado ano (sem paginação).
     */
    public function filter(array $conditions, int $perPage);

    /**
     * Retorna o primeiro feriado que corresponde aos campos informados
     * (ignorando um ID específico, se fornecido).
     */
    public function findDuplicateHoliday(array $data, ?int $excludeId = null);
}
