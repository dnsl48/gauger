<?php


use Debuggy\Gauger\Indicator\Closure;
use Debuggy\Gauger\Indicator\Extra;
use Debuggy\Gauger\Indicator\MemoryPeak;
use Debuggy\Gauger\Indicator\MemoryUsage;
use Debuggy\Gauger\Indicator\Microtime;
use Debuggy\Gauger\Indicator\Preload;
use Debuggy\Gauger\Indicator\TotalDuration;



/**
 * Tests the indicators of the Gauger
 */
class Indicators extends PHPUnit_Framework_TestCase {
	/** Tests the Closure indicator */
	public function testClosure () {
		$ind = new Closure (function () {return 42;});

		$this->assertEquals (42, $ind->gauge ());
	}


	/** Tests the Extra indicator */
	public function testExtra () {
		$ind1 = new Extra;

		$this->assertEquals (42, $ind1->gauge (42));
		$this->assertNull ($ind1->avg (array (1, 2, "3")));
		$this->assertEquals (2, $ind1->avg (array (1, 2, 3)));


		$ind2 = new Extra ('key');

		$this->assertNull ($ind2->gauge (42));
		
		$extra = array ('key' => new StdClass);
		$this->assertSame ($extra['key'], $ind2->gauge ($extra));


		$ind3 = new Extra (null, null, function ($a, $b) {return $a + $b;}, function ($a, $b) {return $a - $b;}, function (array $a) {return max ($a);});
		$this->assertEquals (16, $ind3->sum (2, 14));
		$this->assertEquals (4, $ind3->sub (12, 8));
		$this->assertEquals (6, $ind3->avg (array (1, 2, 6, 3, 4)));


		$this->assertEquals (array ('f' => 1, 'l' => 2), $ind1->sub ("1", "2"));
		$this->assertEquals (42, $ind1->sub (46, 4));
		$this->assertNull ($ind1->sub (null, null));


		$this->assertNull ($ind1->sum (null, null));
		$this->assertEquals (array (1), $ind1->sum (null, 1));
		$this->assertEquals (array (1), $ind1->sum (1, null));
		$this->assertEquals (2, $ind1->sum (1, 1));
		$this->assertEquals (array ("1", "1"), $ind1->sum ("1", "1"));
		$this->assertEquals (array (1), $ind1->sum (null, array (1)));
		$this->assertEquals (array (1), $ind1->sum (array (1), null));
		$this->assertEquals (array (1, 2), $ind1->sum (array (1), array (2)));
		$this->assertEquals (array (array ('key' => 'val'), array (1, 2)), $ind1->sum (array ('key' => 'val'), array (1, 2)));
		$this->assertEquals (array (1, 2, array ('key' => 'val')), $ind1->sum (array (1, 2), array ('key' => 'val')));
	}


	/** Tests the MemoryPeak indicator */
	public function testMemoryPeak () {
		$ind = new MemoryPeak;

		$this->assertTrue (is_int ($ind->gauge ()));
	}


	/** Tests the MemoryUsage indicator */
	public function testMemoryUsage () {
		$ind = new MemoryUsage;

		$this->assertTrue (is_int ($ind->gauge ()));
	}


	/** Tests the Microtime indicator */
	public function testMicrotime () {
		$ind = new Microtime;

		$this->assertTrue (is_float ($ind->gauge ()));
	}


	/** Tests the TotalDuration indicator */
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


	/** Tests the Preload indicator */
	public function testPreload () {
		$ind = new Preload (array (1, 4, 6, 2));

		$this->assertEquals (1, $ind->gauge ());
		$this->assertEquals (4, $ind->gauge ());


		$ind->idle ();
		$this->assertEquals (2, $ind->gauge ());
		$this->assertNull ($ind->gauge ());
	}


	/**
	 * Tests the Indicator
	 *
	 * @depends testPreload
	 */
	public function testIndicator () {
		$ind = new Preload (array (4, 2));


		$this->assertEquals ('Preload', $ind->getName ());

		$ind->setName ('custom indicator');
		$this->assertEquals ('custom indicator', $ind->getName ());


		$this->assertEquals (5, $ind->sum (2, 3));
		$this->assertEquals ('foo + bar', $ind->sum ('foo', 'bar'));


		$this->assertEquals (2, $ind->sub (3, 1));
		$this->assertEquals ('foo - bar', $ind->sub ('foo', 'bar'));


		$this->assertEquals (3, $ind->avg (array (1, 4, 4)));
		$this->assertEquals ('foo + bar + baz / 3', $ind->avg (array ('foo', 'bar', 'baz')));


		$this->assertInstanceOf ('\Debuggy\Gauger\Formatter', $ind->getFormatter ());
	}
}