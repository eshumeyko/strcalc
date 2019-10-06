<?php

namespace eshumeyko\StrcalcBundle;


interface CalculatorInterface
{
    const ERR_DIVISION_BY_ZERO = "На ноль делить нельзя!";

    public function calc(string $str): string;
}