<?php

namespace InovantiBank\Holidays\Exceptions;

use Exception;
use Throwable;

class InvalidYearException extends Exception
{
    /**
     * Cria a exceção com uma mensagem padrão que inclui o ano inválido.
     *
     * @param  int  $year  O ano fornecido que é inválido.
     * @param  int  $code  Código de erro (opcional).
     * @param  Throwable|null  $previous  Exceção anterior (se houver).
     */
    public function __construct(int $year, int $code = 0, ?Throwable $previous = null)
    {
        $message = "O ano fornecido ({$year}) é inválido para consulta de feriados.";
        parent::__construct($message, $code, $previous);
    }
}
