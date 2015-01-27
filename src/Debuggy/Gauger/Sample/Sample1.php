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
	/**
	 * Initializes the sample with a new gauge
	 */
	public function __construct () {
		parent::__construct (new Gauge);

		$gauge = $this->getGauge ();
		$gauge->addDial (new Dial (new Indicator\Microtime));
		$gauge->addDial (new Dial (new Indicator\MemoryUsage));
	}


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
			'Plain' => $plainReporter->recount ($rootRefiner),
			'Stretch' => $plainReporter->recount ($stretchRefiner),
			'Summary' => $summaryReporter->recount ($stretchRefiner),
		), $totals->toArray ());
	}
}