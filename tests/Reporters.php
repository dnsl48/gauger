<?php


use Debuggy\Gauger\Dial;
use Debuggy\Gauger\Gauge;
use Debuggy\Gauger\Indicator\Preload;
use Debuggy\Gauger\Indicator\Extra;
use Debuggy\Gauger\Refiner\Root;
use Debuggy\Gauger\Reporter\Plain;
use Debuggy\Gauger\Reporter\Summary;



/**
 * Tests the reporters of the Gauger
 */
class Reporters extends PHPUnit_Framework_TestCase {
	/** Tests the Plain reporter */
	public function testPlain () {
		$gauge = new Gauge;
		$gauge->addDial (new Dial (new Preload (array (1, 2, 3, 4))));

		$dials = $gauge->getDials();
		$iname = $dials[0]->getIndicator ()->getName ();

		for ($i = 0; $i < 4; ++$i)
			$gauge->stamp ('stamp'.$i);

		$reporter = new Plain;
		$result = $reporter->recount (new Root ($gauge));

		$this->assertCount (4, $result);

		for ($i = 0; $i < 4; ++$i) {
			$this->assertEquals ('stamp'.$i, $result[$i][0]);
			$this->assertArrayHasKey ($iname, $result[$i][1]);
			$this->assertEquals ($i + 1, $result[$i][1][$iname]);
		}
	}


	/** Tests how does the Plain merge several dials */
	public function testPlainMerge () {
		$gauge = new Gauge;
		$gauge->addDial (new Dial (new Preload (array (1, 2, 3, 4))));
		$gauge->addDial (new Dial (new Preload (array (5, 6, 7, 8))));
		$gauge->addDial (new Dial (new Preload (array (9, 10, 11, 12))));

		$dials = $gauge->getDials();
		$iname = $dials[0]->getIndicator ()->getName ();

		for ($i = 0; $i < 4; ++$i)
			$gauge->stamp ('stamp'.$i);

		$reporter = new Plain;
		$result = $reporter->recount (new Root ($gauge));

		$this->assertCount (4, $result);

		for ($i = 0; $i < 4; ++$i) {
			$this->assertEquals ('stamp'.$i, $result[$i][0]);
			$this->assertArrayHasKey ($iname, $result[$i][1]);
			$this->assertCount (3, $result[$i][1][$iname]);
			$this->assertEquals ($i + 1, $result[$i][1][$iname][0]);
			$this->assertEquals ($i + 5, $result[$i][1][$iname][1]);
			$this->assertEquals ($i + 9, $result[$i][1][$iname][2]);
		}
	}


	/** Tests the Summary reporter */
	public function testSummary () {
		$gauge = new Gauge;
		$gauge->addDial (new Dial (new Preload (array (1, 2, 3, 4))));

		$dials = $gauge->getDials();
		$iname = $dials[0]->getIndicator ()->getName ();

		for ($i = 0; $i < 4; ++$i)
			$gauge->stamp ('stamp'.((int)(($i+0.5) / 2)));

		$reporter = new Summary;
		$result = $reporter->recount (new Root ($gauge));

		$this->assertCount (2, $result);

		for ($i = 0; $i < 2; ++$i) {
			$this->assertEquals ('stamp'.$i, $result[$i][0]);
			$this->assertArrayHasKey ($iname, $result[$i][1]);
			$this->assertCount (3, $result[$i][1][$iname]);
			$this->assertArrayHasKey ('cnt', $result[$i][1][$iname]);
			$this->assertArrayHasKey ('sum', $result[$i][1][$iname]);
			$this->assertArrayHasKey ('avg', $result[$i][1][$iname]);

			$this->assertEquals (2, $result[$i][1][$iname]['cnt']);
			$this->assertEquals ($i ? 7 : 3, $result[$i][1][$iname]['sum']);
			$this->assertEquals ($i ? 3.5 : 1.5, $result[$i][1][$iname]['avg']);
		}
	}


	/** Tests how does the Summary merge several dials */
	public function testSummaryMerge () {
		$gauge = new Gauge;
		$gauge->addDial (new Dial (new Preload (array (1, 2, 3, 4))));
		$gauge->addDial (new Dial (new Preload (array (5, 6, 7, 8))));
		$gauge->addDial (new Dial (new Preload (array (9, 10, 11, 12))));

		$dials = $gauge->getDials();
		$iname = $dials[0]->getIndicator ()->getName ();

		for ($i = 0; $i < 4; ++$i)
			$gauge->stamp ('stamp'.((int)(($i+0.5) / 2)));

		$reporter = new Summary;
		$result = $reporter->recount (new Root ($gauge));

		$this->assertCount (2, $result);

		for ($i = 0; $i < 2; ++$i) {
			$this->assertEquals ('stamp'.$i, $result[$i][0]);
			$this->assertArrayHasKey ($iname, $result[$i][1]);
			$this->assertCount (3, $result[$i][1][$iname]);

			$this->assertArrayHasKey ('cnt', $result[$i][1][$iname][0]);
			$this->assertArrayHasKey ('sum', $result[$i][1][$iname][0]);
			$this->assertArrayHasKey ('avg', $result[$i][1][$iname][0]);

			$this->assertArrayHasKey ('cnt', $result[$i][1][$iname][1]);
			$this->assertArrayHasKey ('sum', $result[$i][1][$iname][1]);
			$this->assertArrayHasKey ('avg', $result[$i][1][$iname][1]);

			$this->assertArrayHasKey ('cnt', $result[$i][1][$iname][2]);
			$this->assertArrayHasKey ('sum', $result[$i][1][$iname][2]);
			$this->assertArrayHasKey ('avg', $result[$i][1][$iname][2]);

			$this->assertEquals (2, $result[$i][1][$iname][0]['cnt']);
			$this->assertEquals ($i ? 7 : 3, $result[$i][1][$iname][0]['sum']);
			$this->assertEquals ($i ? 3.5 : 1.5, $result[$i][1][$iname][0]['avg']);

			$this->assertEquals (2, $result[$i][1][$iname][1]['cnt']);
			$this->assertEquals ($i ? 15 : 11, $result[$i][1][$iname][1]['sum']);
			$this->assertEquals ($i ? 7.5 : 5.5, $result[$i][1][$iname][1]['avg']);

			$this->assertEquals (2, $result[$i][1][$iname][2]['cnt']);
			$this->assertEquals ($i ? 23 : 19, $result[$i][1][$iname][2]['sum']);
			$this->assertEquals ($i ? 11.5 : 9.5, $result[$i][1][$iname][2]['avg']);
		}
	}


	/** Tests the Summary visibility of invisible values */
	public function testSummaryVisibility () {
		$gauge = new Gauge;
		$gauge->addDial (new Dial (new Extra));

		$gauge->stamp ('stamp', null);
		$gauge->stamp ('stamp', null);

		$reporter = new Summary;

		$this->assertCount (0, $reporter->recount (new Root ($gauge)));


		$gauge = new Gauge;
		$gauge->addDial (new Dial (new Extra (null, null, function () {return 42;})));

		$gauge->stamp ('stamp', null);
		$gauge->stamp ('stamp', null);

		$reporter = new Summary;
		$result = $reporter->recount (new Root ($gauge));

		$this->assertCount (1, $result);
		$this->assertSame (array ('cnt' => 2, 'sum' => '42'), $result[0][1]['Extra']);


		$gauge = new Gauge;
		$gauge->addDial (new Dial (new Extra (null, null, null, null, function () {return 42;})));

		$gauge->stamp ('stamp', null);
		$gauge->stamp ('stamp', null);

		$reporter = new Summary;
		$result = $reporter->recount (new Root ($gauge));

		$this->assertCount (1, $result);
		$this->assertSame (array ('cnt' => 2, 'avg' => '42'), $result[0][1]['Extra']);
	}
}