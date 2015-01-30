<?php


use Debuggy\Gauger\Gauge as _Gauge;
use Debuggy\Gauger\Dial;
use Debuggy\Gauger\Indicator\Preload;
use Debuggy\Gauger\Filter\Greater;
use Debuggy\Gauger\Filter\Lesser;



/**
 * Tests the Gauge
 */
class Gauge extends PHPUnit_Framework_TestCase {
	/** Tests all methods of the Dial related to stamps */
	public function testStamps () {
		$gauge = new _Gauge;
		$gauge->addDial (new Dial (new Preload (array (1, 2, 3, 4)), new Greater (1)));
		$gauge->addDial (new Dial (new Preload (array (1, 2, 3, 4)), new Lesser (4)));


		$dials = $gauge->getDials ();

		$this->assertCount (2, $dials);


		$gauge->stamp ('one');
		$gauge->stamp ('one');
		$gauge->stamp ('one');
		$gauge->stamp ('one');
		$gauge->stamp ('one');

		$this->assertCount (2, $dials[0]->getCollection ());
		$this->assertCount (2, $dials[1]->getCollection ());


		$gauge->erase ('one');

		$this->assertCount (1, $dials[0]->getCollection ());
		$this->assertCount (1, $dials[1]->getCollection ());
	}
}