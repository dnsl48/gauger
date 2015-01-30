<?php


use Debuggy\Gauger\Dial as _Dial;
use Debuggy\Gauger\Indicator\Preload;
use Debuggy\Gauger\Filter\Greater;



/**
 * Tests the Dial
 */
class Dial extends PHPUnit_Framework_TestCase {
	/** Tests all methods of the Dial related to stamps */
	public function testStamps () {
		$dial = new _Dial (new Preload (array (1, 2, 3, 4, 5)), new Greater (2));

		$this->assertFalse ($dial->stamp ('one'));
		$this->assertFalse ($dial->stamp ('one'));
		$this->assertTrue ($dial->stamp ('one'));


		$dial->idle ();
		$this->assertTrue ($dial->stamp ('one'));
		$this->assertFalse ($dial->stamp ('one'));

		$this->assertCount (2, $dial->getCollection ());

		$dial->erase ('one');

		$this->assertCount (1, $dial->getCollection ());


		$dial->erase ('two');

		$this->assertCount (1, $dial->getCollection ());


		$collection = $dial->getCollection ();

		$this->assertEquals (3, $collection[0]->value);
	}
}