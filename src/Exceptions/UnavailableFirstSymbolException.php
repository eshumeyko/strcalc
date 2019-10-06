<?php

namespace eshumeyko\StrcalcBundle\Exceptions;

class UnavailableFirstSymbolException extends \Exception
{
    public function __construct(
        string $message = 'Выражение не может начинаться с оператора. Проверьте входные данные!',
        int $code = 0
    ) {
        parent::__construct($message, $code);
    }
}