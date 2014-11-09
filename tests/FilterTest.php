<?php


use Debuggy\Gauger;
use Debuggy\Gauger\StretchTimeCalculator;

use Debuggy\Gauger\Filter\Between;
use Debuggy\Gauger\Filter\SequentialClosure;
use Debuggy\Gauger\Filter\SummaryClosure;


class FilterTest extends PHPUnit_Framework_TestCase {
	public function testBetween () {
		$gauger = new StretchTimeCalculator;

		$gauger->addFilter (new Between (0.001));

		$gauger->stamp (0.001, '10');
		$gauger->stamp (0.0019, '10');

		$gauger->stamp (0.01, '1000');
		$gauger->stamp (0.02, '1000');

		$this->assertCount (1, $gauger->getMarks (), 'Only the second mark should be passed the filter');

		$gauger = new StretchTimeCalculator;

		$gauger->stamp (0.001, '10');
		$gauger->stamp (0.0019, '10');

		$gauger->stamp (0.01, '1000');
		$gauger->stamp (0.02, '1000');

		$this->assertCount (1, $gauger->getMarks (array (), array (new Between (0.001))), 'Only the second mark should be passed the filter');
	}

	public function testSequentialClosure () {
		$gauger = new StretchTimeCalculator;

		$gauger->addFilter (new SequentialClosure (function ($mark) {
			return !$mark->extra || !isset ($mark->extra['except']);
		}));

		$gauger->mark ('something');
		$gauger->mark ('something', array ('except' => true));

		$this->assertCount (0, $gauger->getMarks (), 'SequentialClosure has not filtered mark!');
	}

	public function testSummaryClosure () {
		$gauger = new StretchTimeCalculator;

		$gauger->mark ('something');
		$gauger->mark ('something', array ('except' => true));

		$this->assertCount (0, $gauger->getMarks (array (), array (new SummaryClosure (function ($mark) {
			return !$mark->extra || !isset ($mark->extra[2]['except']);
		}))), 'SequentialClosure has not filtered mark!');
	}
}