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
 * This sample contains total time and memory peak usage of the script
 */
class Totals implements Sample {
	public function __construct () {
		$this->_gauge = new Gauge;
		$this->_gauge->addDial (new Dial (new Indicator\TotalDuration));
		$this->_gauge->addDial (new Dial (new Indicator\MemoryPeak));
	}


	/** {@inheritdoc} */
	public function getGauge () {
		return $this->_gauge;
	}


	/** {@inheritdoc} */
	public function toArray () {
		$gauge = $this->getGauge ();
		$reporter = new Reporter\Plain;
		$refiner = new Refiner\Root ($gauge);

		return $reporter->recount ($refiner);
	}


	/** {@inheritdoc} */
	public function toString () {
		$gauge = $this->getGauge ();

		$refiner = new Refiner\Root ($gauge);
		$reporter = new Reporter\Plain;
		$presenter = new Presenter\Txt;

		return $presenter->represent (array ('Totals' => $reporter->recount ($refiner)));
	}



	/**
	 * The instance of a gauge
	 *
	 * @var \Debuggy\Gauger\Gauge
	 */
	private $_gauge;
}