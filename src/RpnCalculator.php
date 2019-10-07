<?php

namespace eshumeyko\StrcalcBundle;

use eshumeyko\StrcalcBundle\Exceptions\{
    ExpectedNumberException,
    UnavailableFirstSymbolException,
    UnavailableOperatorException,
    OddParenthesisException
};

class RpnCalculator implements CalculatorInterface
{
    const LEFT_ASSOC = true;
    const RIGHT_ASSOC = false;

    const MULTIPLICATION = "*";
    const DIVISION = "/";
    const POW = "^";
    const SUMMATION = "+";
    const SUBSTRACTION = "-";

    const AVAILABLE_OPERATORS = [
        self::POW => ["priority" => 3, "assoc" => self::RIGHT_ASSOC],
        self::MULTIPLICATION => ["priority" => 2, "assoc" => self::LEFT_ASSOC],
        self::DIVISION => ["priority" => 2, "assoc" => self::LEFT_ASSOC],
        self::SUMMATION => ["priority" => 1, "assoc" => self::LEFT_ASSOC],
        self::SUBSTRACTION => ["priority" => 1, "assoc" => self::LEFT_ASSOC],
    ];

    private $operatorValues;

    public function __construct()
    {
        $this->operatorValues = array_keys(self::AVAILABLE_OPERATORS);
    }

    public function calc(string $infix): string
    {
        try {
            $postfix = $this->toPostfix($infix);
            $result = $this->calcPostfix($postfix);
        } catch (OddParenthesisException $e) {
            $result = $e->getMessage();
        } catch (UnavailableOperatorException $e) {
            $result = $e->getMessage();
        } catch (\DivisionByZeroError $e) {
            $result = self::ERR_DIVISION_BY_ZERO;
        } catch (\Exception $e) {
            $result = $e->getMessage();
        }

        return $result;
    }

    protected function toPostfix(string $infix): string
    {
        $stack = new \SplStack();
        $postfixArray = [];

        $infix = preg_replace("/\s/", "", $infix);
        $infix = str_replace(",", ".", $infix);
        $infix = str_replace("(-", "(0-", $infix);

        if (substr_count($infix, "(" ) !== substr_count($infix, ")" )) {
            throw new OddParenthesisException();
        }

        $infixArray = str_split($infix);

        if ($infixArray[0] === "-") {
            array_unshift($infixArray, "0");
        } elseif (in_array($infixArray[0], $this->operatorValues)) {
            throw new UnavailableFirstSymbolException();
        }

        $lastIsNumber = true;

        foreach ($infixArray as $key => $value) {

            if (in_array($value, $this->operatorValues)) {

                $endOperatorCycle = false;

                while ($endOperatorCycle != true) {

                    if ($stack->isEmpty()) {
                        $stack->push($value);
                        $endOperatorCycle = true;

                    } else {
                        $lastElement = $stack->pop();


                        $currPriority = isset(self::AVAILABLE_OPERATORS[$value]) ?
                            self::AVAILABLE_OPERATORS[$value]['priority'] : 0;

                        $prevPriority = isset(self::AVAILABLE_OPERATORS[$lastElement]) ?
                            self::AVAILABLE_OPERATORS[$lastElement]['priority'] : 0;

                        $currAssoc = isset(self::AVAILABLE_OPERATORS[$value]) ?
                            self::AVAILABLE_OPERATORS[$value]['assoc'] : 0;

                        if ($currAssoc === self::LEFT_ASSOC) {
                            if ($currPriority > $prevPriority) {
                                $stack[] = $lastElement;
                                $stack[] = $value;
                                $endOperatorCycle = true;
                            } else {
                                $postfixArray[] = $lastElement;
                            }
                        } else {
                            if ($currPriority >= $prevPriority) {
                                $stack->push($lastElement);
                                $stack->push($value);
                                $endOperatorCycle = true;
                            } else {
                                $postfixArray[] = $lastElement;
                            }
                        }
                    }
                }

                $lastIsNumber = false;

            } elseif (is_numeric($value) || $value === '.') {

                if ($lastIsNumber) {
                    $num = array_pop($postfixArray);
                    $postfixArray[] = $num . $value;
                } else {
                    $postfixArray[] = $value;
                    $lastIsNumber = true;
                }
            } elseif ($value == '(') {
                $stack->push($value);
                $lastIsNumber = false;
            } elseif ($value === ')') {
                $openParenthesis = false;

                while ($openParenthesis !== true) {

                    $element = $stack->pop();

                    if ($element === '(') {
                        $openParenthesis = true;
                    } else {
                        $postfixArray[] = $element;
                    }
                }

                $lastIsNumber = false;
            } else {
                throw new UnavailableOperatorException($value);
            }
        }

        $rpn = $postfixArray;

        while (!$stack->isEmpty()) {
            $rpn[] = $stack->pop();
        }

        return implode(' ', $rpn);
    }

    protected function calcPostfix(string $postfix): string
    {
        $stack = new \SplStack();

        $token = strtok($postfix, ' ');

        while ($token !== false) {

            if (in_array($token, $this->operatorValues)) {

                if ($stack->count() < 2) {
                    throw new ExpectedNumberException($token);
                }

                $b = $stack->pop();
                $a = $stack->pop();

                switch ($token) {
                    case self::MULTIPLICATION:
                        $result = $a * $b;
                        break;
                    case self::DIVISION:
                        $result = $a / $b;
                        break;
                    case self::SUMMATION:
                        $result = $a + $b;
                        break;
                    case self::SUBSTRACTION:
                        $result = $a - $b;
                        break;
                    case self::POW:
                        $result = pow($a, $b);
                        break;
                }

                $stack->push($result);

            } elseif (is_numeric($token)) {
                $stack->push($token);
            }

            $token = strtok(' ');
        }

        return $stack->pop();
    }
}
