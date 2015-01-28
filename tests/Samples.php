<?php


use Debuggy\Gauger\Sample\Preload1;
use Debuggy\Gauger\Sample\Sample1;
use Debuggy\Gauger\Sample\Timer;
use Debuggy\Gauger\Sample\Totals;



/**
 * Tests the samples of the Gauger
 */
class Samples extends PHPUnit_Framework_TestCase {
	/** Tests the Totals doesn't make exceptions */
	public function testTotals () {
		$sample = new Totals;

		$sample->getGauge ()->stamp ('test');

		$res = $sample->toArray ();
		$res = $res['Totals'];

		$this->assertCount (1, $res);
		$this->assertEquals ('test', $res[0][0]);

		$this->assertTrue (strlen ($sample->toString ()) > 0);
	}


	/** Tests the Timer doesn't make exceptions */
	public function testTimer () {
		$sample = new Timer (0.0000001);
		$sample->getGauge ()->stamp ('test');
		$sample->getGauge ()->stamp ('test');

		$res = $sample->toArray ();
		$res = $res['Stretch'];

		$this->assertCount (1, $res);
		$this->assertEquals ('test', $res[0][0]);

		$this->assertTrue (strlen ($sample->toString ()) > 0);


		$sample2 = new Timer (null, null, 0.0000001);
		$sample2->getGauge ()->stamp ('test');
		$sample2->getGauge ()->stamp ('test');

		$res = $sample2->toArray ();
		$res = $res['Stretch'];

		$this->assertCount (1, $res);
		$this->assertEquals ('test', $res[0][0]);

		$this->assertTrue (strlen ($sample2->toString ()) > 0);
	}


	/** Tests the Sample1 doesn't make exceptions */
	public function testSample1 () {
		$sample = new Sample1;

		$sample->getGauge ()->stamp ('test');
		$sample->getGauge ()->stamp ('test');

		$res = $sample->toArray ();
		$res = $res['Plain'];

		$this->assertCount (2, $res);
		$this->assertEquals ('test', $res[0][0]);
		$this->assertEquals ('test', $res[1][0]);

		$this->assertTrue (strlen ($sample->toString ()) > 0);
	}


	/** Tests the Preload1 doesn't make exceptions */
	public function testPreload1 () {
		$sample = new Preload1 (array (1, 2), null, array (array (3, 4), array (5, 6)));

		$res = $sample->toArray ();
		$res = $res['Plain'];

		$this->assertCount (2, $res);
		$this->assertEquals ('preval', $res[0][0]);
		$this->assertEquals ('preval', $res[1][0]);

		$this->assertTrue (strlen ($sample->toString ()) > 0);
	}


	/** Tests the Preload1 custom keys */
	public function testPreload1Keys () {
		$sample = new Preload1 (array (array ('stamp1', 1), array ('stamp2', 2)), null, array (array (3, 4), array (5, 6)));

		$res = $sample->toArray ();
		$res = $res['Plain'];

		$this->assertCount (2, $res);
		$this->assertEquals ('stamp1', $res[0][0]);
		$this->assertEquals ('stamp2', $res[1][0]);

		$this->assertTrue (strlen ($sample->toString ()) > 0);
	}
}