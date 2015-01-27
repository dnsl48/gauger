<?php

use Debuggy\Gauger\Filter\AndFilters;
use Debuggy\Gauger\Filter\Between;
use Debuggy\Gauger\Filter\Closure;
use Debuggy\Gauger\Filter\Distinct;
use Debuggy\Gauger\Filter\Equal;
use Debuggy\Gauger\Filter\Greater;
use Debuggy\Gauger\Filter\GreaterOrEqual;
use Debuggy\Gauger\Filter\Head;
use Debuggy\Gauger\Filter\Last;
use Debuggy\Gauger\Filter\Lesser;
use Debuggy\Gauger\Filter\LesserOrEqual;
use Debuggy\Gauger\Filter\Max;
use Debuggy\Gauger\Filter\Min;
use Debuggy\Gauger\Filter\NotEqual;
use Debuggy\Gauger\Filter\OrFilters;
use Debuggy\Gauger\Filter\Tail;

use Debuggy\Gauger\Stamp;



/**
 * Tests the filters of the Gauger
 */
class Filters extends PHPUnit_Framework_TestCase {
	/** Tests Between filter's default behaviour */
	public function testBetween () {
		$stamps = self::$_fix1;

		$fil1 = new Between (1);
		$this->assertEquals ($stamps, $fil1->checkBunch ($stamps));

		$fil2 = new Between (null, 5);
		$this->assertEquals ($stamps, $fil2->checkBunch ($stamps));

		$fil3 = new Between (2, 4);
		$this->assertEquals (array_slice ($stamps, 1, 3), $fil3->checkBunch ($stamps));

		$fil4 = new Between (2.0, 4.0, true);
		$this->assertEquals (array ($stamps[0], $stamps[4]), $fil4->checkBunch ($stamps));
	}


	/**
	 * Tests Between with BCMath mode enabled
	 *
	 * @requires extension bcmath
	 */
	public function testBetweenBcmath () {
		$stamps = self::$_fix1;

		$fil1 = new Between (1, null, false, null);
		$this->assertEquals ($stamps, $fil1->checkBunch ($stamps));

		$fil2 = new Between (null, 5, false, null);
		$this->assertEquals ($stamps, $fil2->checkBunch ($stamps));

		$fil3 = new Between (2, 4, false, null);
		$this->assertEquals (array_slice ($stamps, 1, 3), $fil3->checkBunch ($stamps));

		$fil4 = new Between (2.0, 4.0, true, null);
		$this->assertEquals (array ($stamps[0], $stamps[4]), $fil4->checkBunch ($stamps));
	}


	/**
	 * Tests AndFilters filter relying on Between behaviour
	 *
	 * @depends testBetween
	 */
	public function testAndFilters () {
		$stamps = self::$_fix1;

		$fil1 = new AndFilters (array (new Between (2, null), new Between (null, 4)));
		$this->assertEquals (array_slice ($stamps, 1, 3), $fil1->checkBunch ($stamps));
	}


	/**
	 * Tests OrFilters filter relying on Between behaviour
	 *
	 * @depends testBetween
	 */
	public function testOrFilters () {
		$stamps = self::$_fix1;

		$fil1 = new OrFilters (array (new Between (2, null, true), new Between (null, 4, true)));
		$this->assertEquals (array ($stamps[0], $stamps[4]), $fil1->checkBunch ($stamps));
	}


	/** Tests Closure filter */
	public function testClosure () {
		$stamps = self::$_fix1;

		$fil1 = new Closure (function (Stamp $v) {return $v->value === 3;});
		$this->assertEquals (array ($stamps[2]), $fil1->checkBunch ($stamps));

		$fil2 = new Closure (null, function ($stamps) {return array_slice ($stamps, 1, 2);});
		$this->assertEquals (array ($stamps[1], $stamps[2]), $fil2->checkBunch ($stamps));
	}


	/** Tests Distinct filter */
	public function testDistinct () {
		$stamps = self::$_fix2;

		$fil1 = new Distinct ();
		$this->assertEquals ($stamps, $fil1->checkBunch ($stamps));
		$this->assertEquals (true, $fil1->checkStamp ($stamps[0]));

		$fil2 = new Distinct (false);
		$this->assertEquals (array_values (array_filter ($stamps, function ($v) {return is_int ($v->value);})), $fil2->checkBunch ($stamps));
	}


	/** Tests Equal filter */
	public function testEqual () {
		$stamps = self::$_fix2;

		$fil1 = new Equal (2);
		$this->assertEquals (array ($stamps[2]), $fil1->checkBunch ($stamps));

		$fil2 = new Equal (2, false);
		$this->assertEquals (array ($stamps[2], $stamps[3]), $fil2->checkBunch ($stamps));
	}


	/** Tests NotEqual filter */
	public function testNotEqual () {
		$stamps = self::$_fix2;

		$stamps1 = $stamps;
		array_splice ($stamps1, 2, 1);

		$fil1 = new NotEqual (2);
		$this->assertEquals ($stamps1, $fil1->checkBunch ($stamps));


		$stamps2 = $stamps;
		array_splice ($stamps2, 2, 2);

		$fil2 = new NotEqual (2, false);
		$this->assertEquals ($stamps2, $fil2->checkBunch ($stamps));
	}


	/** Tests Greater filter */
	public function testGreater () {
		$stamps = self::$_fix1;

		$fil1 = new Greater (2);
		$this->assertEquals (array_slice ($stamps, 2), $fil1->checkBunch ($stamps));
	}


	/** Tests GreaterOrEqual filter */
	public function testGreaterOrEqual () {
		$stamps = self::$_fix1;

		$fil1 = new GreaterOrEqual (2);
		$this->assertEquals (array_slice ($stamps, 1), $fil1->checkBunch ($stamps));
	}


	/** Tests Lesser filter */
	public function testLesser () {
		$stamps = self::$_fix1;

		$fil1 = new Lesser (2);
		$this->assertEquals (array ($stamps[0]), $fil1->checkBunch ($stamps));
	}


	/** Tests LesserOrEqual filter */
	public function testLesserOrEqual () {
		$stamps = self::$_fix1;

		$fil1 = new LesserOrEqual (2);
		$this->assertEquals (array_slice ($stamps, 0, 2), $fil1->checkBunch ($stamps));
	}


	/** Tests Head filter */
	public function testHead () {
		$stamps = self::$_fix1;

		$fil1 = new Head ();
		$this->assertEquals (array ($stamps[0]), $fil1->checkBunch ($stamps));

		$fil2 = new Head (2);
		$this->assertTrue ($fil2->checkStamp ($stamps[0]));
		$this->assertTrue ($fil2->checkStamp ($stamps[0]));
		$this->assertFalse ($fil2->checkStamp ($stamps[0])); // third time!
	}


	/** Tests Last filter */
	public function testLast () {
		$stamps = self::$_fix1;

		$fil1 = new Last ();
		$this->assertEquals (array ($stamps[4]), $fil1->checkBunch ($stamps));
		$this->assertTrue ($fil1->checkStamp ($stamps[0]));
	}


	/** Tests Max filter */
	public function testMax () {
		$stamps = self::$_fix1;

		$fil = new Max (2);
		$this->assertEquals (array ($stamps[3], $stamps[4]), $fil->checkBunch ($stamps));
		$this->assertTrue ($fil->checkStamp ($stamps[0]));
	}


	/** Tests Min filter */
	public function testMin () {
		$stamps = self::$_fix1;

		$fil = new Min (2);
		$this->assertEquals (array ($stamps[0], $stamps[1]), $fil->checkBunch ($stamps));
		$this->assertTrue ($fil->checkStamp ($stamps[0]));
	}


	/** Tests Tail filter */
	public function testTail () {
		$stamps = self::$_fix1;

		$fil1 = new Tail ();
		$this->assertEquals (array_slice ($stamps, 1), $fil1->checkBunch ($stamps));

		$fil2 = new Tail (2);
		$this->assertFalse ($fil2->checkStamp ($stamps[0]));
		$this->assertFalse ($fil2->checkStamp ($stamps[0]));
		$this->assertTrue ($fil2->checkStamp ($stamps[0])); // third time!
	}


	/** {@inheritdoc} */
	public static function setUpBeforeClass () {
		$stamp = new Stamp;
		$stamp->id = 'one';
		$stamp->value = 1;
		static::$_fix1[] = $stamp;

		$stamp = new Stamp;
		$stamp->id = 'one';
		$stamp->value = 2;
		static::$_fix1[] = $stamp;

		$stamp = new Stamp;
		$stamp->id = 'one';
		$stamp->value = 3;
		static::$_fix1[] = $stamp;

		$stamp = new Stamp;
		$stamp->id = 'one';
		$stamp->value = 4;
		static::$_fix1[] = $stamp;

		$stamp = new Stamp;
		$stamp->id = 'one';
		$stamp->value = 5;
		static::$_fix1[] = $stamp;


		$stamp = new Stamp;
		$stamp->id = 'one';
		$stamp->value = 1;
		static::$_fix2[] = $stamp;

		$stamp = new Stamp;
		$stamp->id = 'one';
		$stamp->value = 1.0;
		static::$_fix2[] = $stamp;

		$stamp = new Stamp;
		$stamp->id = 'one';
		$stamp->value = 2;
		static::$_fix2[] = $stamp;

		$stamp = new Stamp;
		$stamp->id = 'one';
		$stamp->value = 2.0;
		static::$_fix2[] = $stamp;

		$stamp = new Stamp;
		$stamp->id = 'one';
		$stamp->value = 3;
		static::$_fix2[] = $stamp;

		$stamp = new Stamp;
		$stamp->id = 'one';
		$stamp->value = 3.0;
		static::$_fix2[] = $stamp;
	}



	/**
	 * Fixture1
	 *
	 * @var Stamp[]
	 */
	private static $_fix1 = array ();


	/**
	 * Fixture2
	 *
	 * @var Stamp[]
	 */
	private static $_fix2 = array ();
}