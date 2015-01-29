<?php


namespace Debuggy\Gauger\Sample;


use Debuggy\Gauger\Dial;
use Debuggy\Gauger\Gauge;
use Debuggy\Gauger\Indicator;
use Debuggy\Gauger\Refiner;
use Debuggy\Gauger\Reporter;
use Debuggy\Gauger\Sample;



/**
 * This sample contains total time and memory peak usage of the script
 */
class Totals extends Sample {
	/** {@inheritdoc} */
	public function toArray () {
		$gauge = $this->getGauge ();

		$refiner = new Refiner\Root ($gauge);
		$reporter = new Reporter\Plain;

		return array ('Totally' => $reporter->recount ($refiner));
	}


	/** {@inheritdoc} */
	protected function initGauge (Gauge $gauge) {
		$gauge->addDial (new Dial (new Indicator\TotalDuration));
		$gauge->addDial (new Dial (new Indicator\MemoryPeak));
		$gauge->addDial (new Dial (new Indicator\Extra));
	}
}