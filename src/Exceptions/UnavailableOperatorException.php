<?php

namespace eshumeyko\StrcalcBundle\Exceptions\UnavailableOperatorException;

class UnavailableOperatorException extends \Exception
{
    public function __construct(string $operator, int $code = 0)
    {

        $message = sprintf('Недопустимый оператор "%s", список поддерживаемых операторов: [%s]',
            $operator,
            implode(' ', array_keys(RpnCalculator::AVAILABLE_OPERATORS))
        );

        parent::__construct($message, $code);
    }

}