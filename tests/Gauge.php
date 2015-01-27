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


	/**
	 * Tests the Gauge method benchmark
	 *
	 * @depends testStamps
	 * @expectedException Exception
	 * @expectedExceptionMessage bench2
	 */
	public function testBenchmark () {
		$gauge = new _Gauge;
		$gauge->addDial (new Dial (new Preload (array (1, 2, 3, 4))));

		$benchValue = $gauge->benchmark (function () {return 'benchValue';}, 'bench1');

		$this->assertEquals ('benchValue', $benchValue);

		$dials = $gauge->getDials ();
		$this->assertCount (1, $dials);


		$collection = $dials[0]->getCollection ();
		$this->assertCount (2, $collection);


		$this->assertEquals (1, $collection[0]->value);
		$this->assertEquals (2, $collection[1]->value);


		try {
			$gauge->benchmark (function () {throw new Exception ('bench2');}, 'bench2');

		} catch (Exception $e) {
			$collection = $dials[0]->getCollection ();
			$this->assertCount (4, $collection);

			$this->assertEquals (3, $collection[2]->value);
			$this->assertEquals (4, $collection[3]->value);
			$this->assertNotEmpty ($collection[3]->extra);
			$this->assertArrayHasKey ('exception', $collection[3]->extra);
			$this->assertSame ($e, $collection[3]->extra['exception']);

			throw $e;
		}
	}
}