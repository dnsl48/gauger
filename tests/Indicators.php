<?php


use Debuggy\Gauger\Indicator\Closure;
use Debuggy\Gauger\Indicator\MemoryPeak;
use Debuggy\Gauger\Indicator\MemoryUsage;
use Debuggy\Gauger\Indicator\Microtime;
use Debuggy\Gauger\Indicator\Preload;
use Debuggy\Gauger\Indicator\TotalDuration;



/**
 * Tests the indicators of the Gauger
 */
class Indicators extends PHPUnit_Framework_TestCase {
	/** Tests Closure indicator */
	public function testClosure () {
		$ind = new Closure (function () {return 42;});

		$this->assertEquals (42, $ind->gauge ());
	}


	/** Tests MemoryPeak indicator */
	public function testMemoryPeak () {
		$ind = new MemoryPeak;

		$this->assertTrue (is_int ($ind->gauge ()));
	}


	/** Tests MemoryUsage indicator */
	public function testMemoryUsage () {
		$ind = new MemoryUsage;

		$this->assertTrue (is_int ($ind->gauge ()));
	}


	/** Tests Microtime indicator */
	public function testMicrotime () {
		$ind = new Microtime;

		$this->assertTrue (is_float ($ind->gauge ()));
	}


	/** Tests TotalDuration indicator */
	public function testTotalDuration () {
		$ind1 = new TotalDuration;

		$this->assertTrue (is_numeric ($ind1->gauge ()));


		unset ($_SERVER['REQUEST_TIME_FLOAT']);

		$ind2 = new TotalDuration;

		$this->assertTrue (is_numeric ($ind2->gauge ()));

		
		unset ($_SERVER['REQUEST_TIME']);

		$ind3 = new TotalDuration;

		$this->assertTrue (is_numeric ($ind3->gauge ()));
	}


	/** Tests Preload indicator */
	public function testPreload () {
		$ind = new Preload (array (1, 4, 6, 2));

		$this->assertEquals (1, $ind->gauge ());
		$this->assertEquals (4, $ind->gauge ());


		$ind->idle ();
		$this->assertEquals (2, $ind->gauge ());
		$this->assertNull ($ind->gauge ());
	}


	/**
	 * Tests base Indicator
	 *
	 * @depends testPreload
	 */
	public function testIndicator () {
		$ind = new Preload (array (4, 2));

		$this->assertEquals ('Preload', $ind->getName ());
		$this->assertEquals (5, $ind->sum (2, 3));
		$this->assertEquals ('foo + bar', $ind->sum ('foo', 'bar'));
		$this->assertEquals (2, $ind->sub (3, 1));
		$this->assertEquals ('foo - bar', $ind->sub ('foo', 'bar'));
		$this->assertEquals (3, $ind->avg (array (1, 4, 4)));
		$this->assertEquals ('foo + bar + baz / 3', $ind->avg (array ('foo', 'bar', 'baz')));
		$this->assertInstanceOf ('\Debuggy\Gauger\Formatter', $ind->getFormatter ());
	}
}