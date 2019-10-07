<?php

require __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use eshumeyko\StrcalcBundle\RpnCalculator;
use eshumeyko\StrcalcBundle\Exceptions\{
    ExpectedNumberException,
    UnavailableFirstSymbolException,
    UnavailableOperatorException,
    OddParenthesisException
};

class RpnCalculatorTest extends TestCase
{
    private $rpnCalculator;

    /* infix -> postfix -> result */
    protected static $testData = [
        ["1 + 1", "1 1 +", "2"],
        ["2+2", "2 2 +", "4"],
        ["4 +7", "4 7 +", "11"],
        ["2 +11", "2 11 +", "13"],
        ["-2 + 11", "0 2 - 11 +", "9"],
        ["1 - 1", "1 1 -", "0"],
        ["2-9", "2 9 -", "-7"],
        ["4 -7", "4 7 -", "-3"],
        ["2 -11", "2 11 -", "-9"],
        ["1 * 1", "1 1 *", "1"],
        ["2*2", "2 2 *", "4"],
        ["4 *7", "4 7 *", "28"],
        ["2 *11", "2 11 *", "22"],
        ["1 / 1", "1 1 /", "1"],
        ["2/2", "2 2 /", "1"],
        ["4 /7", "4 7 /", "0.57142857142857"],
        ["2 /11", "2 11 /", "0.18181818181818"],
        ["1 ^ 1", "1 1 ^", "1"],
        ["2^2", "2 2 ^", "4"],
        ["4 ^7", "4 7 ^", "16384"],
        ["2 ^11", "2 11 ^", "2048"],
        ["(1 + 2)", "1 2 +", "3"],
        ["(1 - 2)", "1 2 -", "-1"],
        ["(-2 * 5)", "0 2 5 * -", "-10"],
        ["(0.1 / 6)", "0.1 6 /", "0.016666666666667"],
        ["(1 + 2)/(9 + 1)", "1 2 + 9 1 + /", "0.3"],
        ["(1 - 2) / 8", "1 2 - 8 /", "-0.125"],
        ["(-2 * 5) / 9 - 1", "0 2 5 * - 9 / 1 -", "-2.1111111111111"],
        ["(0.9 / 6) ^ 2 - (1 / 8 - 8)", "0.9 6 / 2 ^ 1 8 / 8 - -", "7.8975"],
        ["0 - 0", "0 0 -", "0"],
        ["-10 * (-9)", "0 10 0 9 - * -", "90"]
    ];

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->rpnCalculator = new RpnCalculator();
    }

    public static function getMethod($methodName)
    {
        $class = new \ReflectionClass(RpnCalculator::class);
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);
        return $method;
    }

    /**
     * @dataProvider infixToPostfixDataProvider
     */
    public function testInfixToPostfix($infix, $expectedpostfix)
    {
        $toPostfix = self::getMethod('toPostfix');
        $postfix = $toPostfix->invokeArgs($this->rpnCalculator, [$infix]);
        $this->assertEquals($expectedpostfix, $postfix);
    }

    /**
     * @dataProvider postfixToResultDataProvider
     */
    public function testCalcPostfix($postfix, $expectedResult)
    {
        $calcPostfix = self::getMethod('calcPostfix');
        $result = $calcPostfix->invokeArgs($this->rpnCalculator, [$postfix]);
        $this->assertEquals($expectedResult, $result);
    }

    public function testOddParenthesisException()
    {
        $this->expectException(OddParenthesisException::class);
        $toPostfix = self::getMethod('toPostfix');
        $toPostfix->invokeArgs($this->rpnCalculator, ["(1 + 3))"]);
    }

    public function testUnavailableFirstSymbolException()
    {
        $this->expectException(UnavailableFirstSymbolException::class);
        $toPostfix = self::getMethod('toPostfix');
        $toPostfix->invokeArgs($this->rpnCalculator, ["*1"]);
    }

    public function testUnavailableOperatorException()
    {
        $this->expectException(UnavailableOperatorException::class);
        $toPostfix = self::getMethod('toPostfix');
        $toPostfix->invokeArgs($this->rpnCalculator, ["1 % 9"]);
    }

    public function testExpectedNumberException()
    {
        $this->expectException(ExpectedNumberException::class);
        $calcPostfix = self::getMethod('calcPostfix');
        $calcPostfix->invokeArgs($this->rpnCalculator, ["(1 + )"]);
    }

    public static function infixToPostfixDataProvider()
    {
        return self::$testData;
    }

    public static function postfixToResultDataProvider()
    {
        foreach (self::$testData as $dataSet) {
            $testData[] = array_slice($dataSet, 1);
        }
        return $testData;
    }
}