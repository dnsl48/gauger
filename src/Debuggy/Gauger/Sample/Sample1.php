<?php


namespace Debuggy\Gauger\Sample;


use Debuggy\Gauger\Dial;
use Debuggy\Gauger\Gauge;
use Debuggy\Gauger\Indicator;
use Debuggy\Gauger\Presenter;
use Debuggy\Gauger\Refiner;
use Debuggy\Gauger\Reporter;
use Debuggy\Gauger\Sample;



/**
 * Measuring of microtime and memory usage.
 * Report in three parts: all stamps, stretches between stamps and the summary of stretches.
 */
class Sample1 implements Sample {
	/**
	 * Initializes the sample with a new gauge
	 */
	public function __construct () {
		$this->_gauge = new Gauge;
		$this->_gauge->addDial (new Dial (new Indicator\Microtime));
		$this->_gauge->addDial (new Dial (new Indicator\MemoryUsage));
	}


	/** {@inheritdoc} */
	public function getGauge () {
		return $this->_gauge;
	}


	/** {@inheritdoc} */
	public function toArray () {
		$gauge = $this->getGauge ();
		$refiner = new Refiner\Root ($gauge);
		$reporter = new Reporter\Plain;

		return $reporter->recount ($refiner);
	}


	/** {@inheritdoc} */
	public function toString () {
		$gauge = $this->getGauge ();

		$rootRefiner = new Refiner\Root ($gauge);
		$stretchRefiner = new Refiner\Cache (new Refiner\Stretch ($rootRefiner));

		$plainReporter = new Reporter\Plain;
		$summaryReporter = new Reporter\Summary;

		$presenter = new Presenter\Txt;

		$totals = new Totals;
		$totals->getGauge ()->stamp ('Sample1');

		return implode (PHP_EOL, array (
			$presenter->represent (array ('Plain' => $plainReporter->recount ($rootRefiner))),
			$presenter->represent (array ('Stretch' => $plainReporter->recount ($stretchRefiner))),
			$presenter->represent (array ('Summary' => $summaryReporter->recount ($stretchRefiner))),
			$totals->toString ()
		));
	}



	/**
	 * The instance of a gauge
	 *
	 * @var \Debuggy\Gauger\Gauge
	 */
	protected $_gauge;
}