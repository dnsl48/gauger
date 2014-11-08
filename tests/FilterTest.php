<?php


use Debuggy\Gauger;
use Debuggy\Gauger\StretchCalculator;
use Debuggy\Gauger\StretchAccumulator;

use Debuggy\Gauger\Filter\Duration;
use Debuggy\Gauger\Filter\SequentialClosure;
use Debuggy\Gauger\Filter\SummaryClosure;


class FilterTest extends PHPUnit_Framework_TestCase {
	public function testDuration () {
		$gauger = new StretchCalculator;

		$gauger->addFilter (new Duration (0.001));

		$gauger->stamp (0.001, '10');
		$gauger->stamp (0.0019, '10');

		$gauger->stamp (0.01, '1000');
		$gauger->stamp (0.02, '1000');

		$this->assertCount (1, $gauger->getMarks (), 'Only the second mark should be passed the filter');

		$gauger = new StretchCalculator;

		$gauger->gauge (function () {usleep (10);}, array (), '10');
		$gauger->gauge (function () {usleep (1000);}, array (), '1000');

		$this->assertCount (1, $gauger->getMarks (array (), array (new Duration (0.001))), 'Only the second mark should be passed the filter');
	}

	public function testSequentialClosure () {
		$gauger = new StretchCalculator;

		$gauger->addFilter (new SequentialClosure (function ($mark) {
			return !$mark->extra || !isset ($mark->extra['except']);
		}));

		$gauger->mark ('something');
		$gauger->mark ('something', array ('except' => true));

		$this->assertCount (0, $gauger->getMarks (), 'SequentialClosure has not filtered mark!');
	}

	public function testSummaryClosure () {
		$gauger = new StretchCalculator;

		$gauger->mark ('something');
		$gauger->mark ('something', array ('except' => true));

		$this->assertCount (0, $gauger->getMarks (array (), array (new SummaryClosure (function ($mark) {
			return !$mark->extra || !isset ($mark->extra[2]['except']);
		}))), 'SequentialClosure has not filtered mark!');
	}
}