# strcalc
String Calculator Symfony4 Bundle


### Install

- install symfony4 https://symfony.com/doc/current/setup
- install strcalc via composer

`$ composer require eshumeyko/strcalc`

### Usage

    
	...
		use eshumeyko\StrcalcBundle\Calculator;
		
	...
		
		$infix = "1 + 2 / 4 - 9 * (1 - 7/3.5)^2";
		$calculator = new Calculator();		
		$result = $calculator->calc($infix); 
		// -7.5
		
	...
    
    
	
