<?php


namespace Debuggy\Gauger\Sample;


use Debuggy\Gauger\Dial;
use Debuggy\Gauger\Gauge;
use Debuggy\Gauger\Indicator;
use Debuggy\Gauger\Refiner;
use Debuggy\Gauger\Reporter;
use Debuggy\Gauger\Sample;



/**
 * Measuring of microtime and memory usage.
 * Report in three parts: all stamps, stretches between stamps and the summary of stretches.
 */
class Sample1 extends Sample {
	/** {@inheritdoc} */
	public function toArray () {
		$gauge = $this->getGauge ();

		$rootRefiner = new Refiner\Root ($gauge);
		$stretchRefiner = new Refiner\Cache (new Refiner\Stretch ($rootRefiner));

		$plainReporter = new Reporter\Plain;
		$summaryReporter = new Reporter\Summary;

		$totals = new Totals;
		$totals->getGauge ()->stamp ('Sample1');

		return array_merge (array (
			'Plainy' => $plainReporter->recount ($rootRefiner),
			'Stretchy' => $plainReporter->recount ($stretchRefiner),
			'Summary' => $summaryReporter->recount ($stretchRefiner),
		), $totals->toArray ());
	}


	/** {@inheritdoc} */
	protected function initGauge (Gauge $gauge) {
		$gauge->addDial (new Dial (new Indicator\Microtime));
		$gauge->addDial (new Dial (new Indicator\MemoryUsage));
		$gauge->addDial (new Dial (new Indicator\Extra));
	}
}