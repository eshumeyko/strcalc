<?php

namespace eshumeyko\StrcalcBundle\Exceptions;

class ExcpectedNumberException extends \Exception
{
    public function __construct(
        string $operator,
        int $code = 0
    ) {
        $message = sprintf('Пропущен операнд для оператора "%s". Проверьте входные данные!', $operator);
        parent::__construct($message, $code);
    }
}