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
	public function __construct () {
		parent::__construct (new Gauge);

		$gauge = $this->getGauge ();
		$gauge->addDial (new Dial (new Indicator\TotalDuration));
		$gauge->addDial (new Dial (new Indicator\MemoryPeak));
	}


	/** {@inheritdoc} */
	public function toArray () {
		$gauge = $this->getGauge ();

		$refiner = new Refiner\Root ($gauge);
		$reporter = new Reporter\Plain;

		return array ('Totals' => $reporter->recount ($refiner));
	}
}