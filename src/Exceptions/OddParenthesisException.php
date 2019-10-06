<?php

namespace eshumeyko\strcalc\StrcalcBundle\Exceptions\OddParenthesisException;

class OddParenthesisException extends \Exception
{
    public function __construct(
        string $message = 'Кол-во открывающих скобок не равно кол-ву закрывающих! Проверьте входные данные!',
        int $code = 0
    ) {
        parent::__construct($message, $code);
    }
}