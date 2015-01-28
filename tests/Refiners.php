<?php


use Debuggy\Gauger\Dial;
use Debuggy\Gauger\Filter\Between;
use Debuggy\Gauger\Gauge;
use Debuggy\Gauger\Indicator\Preload;
use Debuggy\Gauger\Refiner\Cache;
use Debuggy\Gauger\Refiner\Filter;
use Debuggy\Gauger\Refiner\Root;
use Debuggy\Gauger\Refiner\Stretch;
use Debuggy\Gauger\Refiner\Total;



/**
 * Tests the refiners of the Gauger
 */
class Refiners extends PHPUnit_Framework_TestCase {
	/** Tests the Root refiner */
	public function testRoot () {
		$gauge = $this->_gauge;
		$root = new Root ($gauge);

		$indicators = $root->getIndicators ();
		$this->assertCount (1, $indicators);

		$stamps = $root->getStamps ();
		$this->assertCount (1, $stamps);
		$this->assertCount (4, $stamps[0]);
	}


	/**
	 * Tests the Cache refiner
	 *
	 * @depends testRoot
	 */
	public function testCache () {
		$cache = new Cache (new Root ($this->_gauge));

		$indicators = $cache->getIndicators ();
		$this->assertCount (1, $indicators);
		$this->assertSame ($indicators, $cache->getIndicators ());

		$stamps = $cache->getStamps ();
		$this->assertCount (1, $stamps);
		$this->assertCount (4, $stamps[0]);
		$this->assertSame ($stamps, $cache->getStamps ());
	}


	/**
	 * Tests the Filter refiner
	 *
	 * @depends testRoot
	 */
	public function testFilter () {
		$filter = new Filter (new Root ($this->_gauge), new Between (2, 3));

		$stamps = $filter->getStamps ();
		$this->assertCount (1, $stamps);
		$this->assertCount (2, $stamps[0]);

		$this->assertEquals (2, $stamps[0][0]->value);
		$this->assertEquals (3, $stamps[0][1]->value);
	}


	/**
	 * Tests the Stretch refiner
	 *
	 * @depends testRoot
	 */
	public function testStretch () {
		$stretch = new Stretch (new Root ($this->_gauge));

		$stamps = $stretch->getStamps ();
		$this->assertCount (1, $stamps);
		$this->assertCount (2, $stamps[0]);

		$this->assertEquals (1, $stamps[0][0]->value);
		$this->assertEquals (1, $stamps[0][1]->value);


		$gauge = new Gauge;
		$gauge->addDial (new Dial (new Preload (array (1, 2, 3))));

		$gauge->stamp ('total');
		$gauge->stamp ('total<-2');
		$gauge->stamp ('total');

		$stretch = new Stretch (new Root ($gauge));
		$stamps = $stretch->getStamps ();
		$this->assertCount (1, $stamps);
		$this->assertCount (2, $stamps[0]);

		$this->assertEquals ('total', $stamps[0][0]->id);
		$this->assertEquals (2, $stamps[0][0]->value);

		$this->assertEquals ('total<-2', $stamps[0][1]->id);
		$this->assertEquals (1, $stamps[0][1]->value);


		$gauge = new Gauge;
		$gauge->addDial (new Dial (new Preload (array (1, 2, 3))));

		$gauge->stamp ('to<-tal');
		$gauge->stamp ('to<-tal->se-cond');
		$gauge->stamp ('to<-tal');

		$stretch = new Stretch (new Root ($gauge));
		$stamps = $stretch->getStamps ();
		$this->assertCount (1, $stamps);
		$this->assertCount (2, $stamps[0]);

		$this->assertEquals ('to<-tal', $stamps[0][0]->id);
		$this->assertEquals (1, $stamps[0][0]->value);

		$this->assertEquals ('to<-tal->se-cond', $stamps[0][1]->id);
		$this->assertEquals (1, $stamps[0][1]->value);


		$gauge = new Gauge;
		$gauge->addDial (new Dial (new Preload (array (1, 2, 3, 4, 5))));

		$gauge->stamp ('total');
		$gauge->stamp ('total<-2');
		$gauge->stamp ('total<-2->3');
		$gauge->stamp ('total<-2->3');
		$gauge->stamp ('total');

		$stretch = new Stretch (new Root ($gauge));
		$stamps = $stretch->getStamps ();
		$this->assertCount (1, $stamps);
		$this->assertCount (3, $stamps[0]);

		$this->assertEquals ('total', $stamps[0][0]->id);
		$this->assertEquals (4, $stamps[0][0]->value);

		$this->assertEquals ('total<-2', $stamps[0][1]->id);
		$this->assertEquals (2, $stamps[0][1]->value);

		$this->assertEquals ('total<-2->3', $stamps[0][2]->id);
		$this->assertEquals (1, $stamps[0][2]->value);
	}


	/**
	 * Tests the Total refiner
	 *
	 * @depends testRoot
	 */
	public function testTotal () {
		$total = new Total (new Root ($this->_gauge));

		$stamps = $total->getStamps ();

		$this->assertCount (1, $stamps);
		$this->assertCount (1, $stamps[0]);

		$this->assertEquals ('one', $stamps[0][0]->id);
		$this->assertEquals (10, $stamps[0][0]->value);
	}


	/**
	 * Makes a new Gauge instance
	 *
	 * @return Gauge
	 */
	public function setUp () {
		$gauge = new Gauge;
		$gauge->addDial (new Dial (new Preload (array (1, 2, 3, 4))));

		$gauge->stamp ('one');
		$gauge->stamp ('one');
		$gauge->stamp ('one');
		$gauge->stamp ('one');

		$this->_gauge = $gauge;
	}



	/**
	 * Gauge instance
	 *
	 * @var Gauge
	 */
	private $_gauge;
}