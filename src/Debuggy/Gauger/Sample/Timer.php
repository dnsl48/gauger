<?php


namespace Debuggy\Gauger\Sample;


use Debuggy\Gauger\Dial;
use Debuggy\Gauger\Gauge;
use Debuggy\Gauger\Indicator;
use Debuggy\Gauger\Refiner;
use Debuggy\Gauger\Reporter;
use Debuggy\Gauger\Sample;



/**
 * Timer sample contains only microtime measuring and two parts of report: stretches and their summary
 */
class Timer extends Sample {
	public function __construct () {
		parent::__construct (new Gauge);

		$this->getGauge ()->addDial (new Dial (new Indicator\Microtime));
	}


	/** {@inheritdoc} */
	public function toArray () {
		$gauge = $this->getGauge ();

		$stretchRefiner = new Refiner\Cache (new Refiner\Stretch (new Refiner\Root ($gauge)));

		$plainReporter = new Reporter\Plain;
		$summaryReporter = new Reporter\Summary;

		return array (
			'Stretch' => $plainReporter->recount ($stretchRefiner),
			'Summary' => $summaryReporter->recount ($stretchRefiner)
		);
	}
}