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
 * Timer sample contains only microtime measuring and two parts of report: stretches and their summary
 */
class Timer implements Sample {
	public function __construct () {
		$this->_gauge = new Gauge;
		$this->_gauge->addDial (new Dial (new Indicator\Microtime));
	}


	/** {@inheritdoc} */
	public function getGauge () {
		return $this->_gauge;
	}


	/** {@inheritdoc} */
	public function toArray () {
		$gauge = $this->getGauge ();
		$reporter = new Reporter\Plain;
		$refiner = new Refiner\Stretch (new Refiner\Root ($gauge));

		return $reporter->recount ($refiner);
	}


	/** {@inheritdoc} */
	public function toString () {
		$gauge = $this->getGauge ();

		$stretchRefiner = new Refiner\Cache (new Refiner\Stretch (new Refiner\Root ($gauge)));

		$plainReporter = new Reporter\Plain;
		$summaryReporter = new Reporter\Summary;

		$presenter = new Presenter\Txt;

		return implode (PHP_EOL, array (
			$presenter->represent (array ('Stretch' => $plainReporter->recount ($stretchRefiner))),
			$presenter->represent (array ('Summary' => $summaryReporter->recount ($stretchRefiner)))
		));
	}



	/**
	 * The instance of a gauge
	 *
	 * @var \Debuggy\Gauger\Gauge
	 */
	private $_gauge;
}