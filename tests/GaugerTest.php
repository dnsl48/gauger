<?php


use Debuggy\Gauger;
use Debuggy\Gauger\StretchCalculator;
use Debuggy\Gauger\StretchAccumulator;

use Debuggy\Gauger\Filter\SequentialFalse;
use Debuggy\Gauger\Filter\SequentialClosure;
use Debuggy\Gauger\Filter\SummaryClosure;

use Debuggy\Gauger\Formatter\Txt as TxtFormatter;


class GaugerTest extends PHPUnit_Framework_TestCase {
	public function testGetStatic () {
		$stretchCalculator = StretchCalculator::getStatic ();
		$this->assertInstanceOf ('Debuggy\Gauger\StretchCalculator', $stretchCalculator, 'getStatic has returned instance of wrong class');

		$stretchAccumulator = StretchAccumulator::getStatic ('accumulator name');
		$this->assertInstanceOf ('Debuggy\Gauger\StretchAccumulator', $stretchAccumulator, 'getStatic has returned instance of wrong class');
		$this->assertEquals ($stretchAccumulator->getName (), 'accumulator name', 'getStatic has not initialized gauger with name');
		$this->assertSame ($stretchAccumulator, Gauger::getStatic ('accumulator name'), 'getStatic does not remember the history');
	}

	public function testGauge () {
		$acc = new StretchCalculator ('Gauge');

		$callback = function () {};

		$acc->gauge ($callback, array (), 'empty');

		$marks = $acc->getMarks ();

		$this->assertCount (1, $marks, 'Wrong count of harvested marks');

		$callbackWithException = function () {throw new Exception ('exception_message');};


		try {
			$acc->gauge ($callbackWithException, array (), 'exception');
		} catch (Exception $e) {
			$this->assertEquals ($e->getMessage (), 'exception_message');
		}

		$marks = $acc->getMarks ();

		$this->assertCount (2, $marks, 'Wrong count of harvested marks');

		$this->assertArrayHasKey ('2', $marks['exception']->extra, 'Exception should be thrown with the second mark');
		$this->assertArrayHasKey ('exception', $marks['exception']->extra['2'], 'Exception should be stored in extra info with key "exception"');
		$this->assertEquals ($marks['exception']->extra['2']['exception']->getMessage (), 'exception_message', 'Exception message is wrong');
	}

	public function testFilters () {
		$acc = new StretchAccumulator ();

		$acc->addFilter (new SequentialFalse);

		$this->assertCount (1, $acc->getFilters (), 'Filter has not been registered in gauger!');

		$acc->gauge (function () {}, array (), 'test');

		$this->assertCount (0, $acc->getMarks (), 'Filter has not been working');

		$acc->resetFilters ();

		$this->assertCount (0, $acc->getFilters (), 'Filter has not been deleted from gauger!');
	}

	public function testToString () {
		$gauger = new StretchAccumulator;
		$formatter = new TxtFormatter;

		$this->assertEquals ($formatter->gauger ($gauger), (string) $gauger, 'TxtFormatter has made different result with gauger->__toString');
	}

	public function testStretchAccumulator () {
		$acc = new StretchAccumulator ();

		$acc->mark ('100');
		$acc->mark ('100');

		$this->assertCount (2, $acc->getMarks (), 'Mark has not been stored');

		$acc->reset ();

		$acc->addFilter (new SequentialClosure (function () {return true;}));

		$this->assertCount (0, $acc->getMarks (), 'Gauger has not been resetted');

		$acc->mark ('100');
		$acc->mark ('100');
		$acc->mark ('100');
		$acc->mark ('100');
		$acc->mark ('100');
		$acc->mark ('100');

		$acc->mark ('100');
		$acc->mark ('100', array ('except' => true));

		$this->assertCount (3, $acc->getMarks (
			array (new SequentialClosure (function ($mark) {return !$mark->extra || !isset ($mark->extra['latter']['except']);})),
			array (new SummaryClosure (function ($mark) {return false;}))
		), 'Filters have not worked correctly');

		$acc = new StretchAccumulator ();
		$acc->mark ('first');
		$acc->mark ('first');

		$acc->mark ('second');
		$acc->mark ('second', array ('except' => true));

		$acc->mark ('third');
		$acc->mark ('third');

		$acc->mark ('fourth');
		$acc->mark ('fourth');

		$acc->mark ('fifth');
		$acc->mark ('fifth', array ('except' => true));

		$acc->mark ('sixth');
		$acc->mark ('sixth');

		$marks = $acc->getMarks ();

		$this->assertEquals (1, $marks[0]->number);
		$this->assertEquals (2, $marks[1]->number);

		$marks = $acc->getMarks (array (new SequentialClosure (function ($mark) {return !$mark->extra;})));

		$this->assertEquals (1, $marks[0]->number);
		$this->assertEquals (3, $marks[1]->number);
		$this->assertEquals (4, $marks[2]->number);
		$this->assertEquals (6, $marks[3]->number);
	}

	public function testStretchCalculator () {
		$cal = new StretchCalculator ();

		$cal->mark ('100');
		$cal->mark ('100');

		$this->assertCount (1, $cal->getMarks (), 'Mark has not been stored');

		$cal->reset ();

		$cal->addFilter (new SequentialClosure (function () {return true;}));

		$this->assertCount (0, $cal->getMarks (), 'Gauger has not been resetted');
	}
}