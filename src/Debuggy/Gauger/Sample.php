<?php


namespace Debuggy\Gauger;


use Debuggy\Gauger\Presenter\Txt;



/**
 * Sample of the Gauger components usage.
 * It also can be used as a pattern for some often situations.
 */
abstract class Sample {
	/**
	 * Initializes the object with a gauge
	 *
	 * @param Gauge $gauge Gauge instance
	 */
	protected function __construct (Gauge $gauge) {
		$this->_gauge = $gauge;
	}


	/**
	 * Returns an instance of a gauge
	 *
	 * @return Gauge
	 */
	public function getGauge () {
		return $this->_gauge;
	}


	/**
	 * Makes the recount through the Txt reporter
	 *
	 * @return string
	 */
	public function toString () {
		$presenter = new Txt;

		return $presenter->represent ($this->toArray ());
	}


	/**
	 * Returns the recount as an array
	 *
	 * @return array
	 */
	abstract public function toArray ();



	/**
	 * An instance of the Gauge
	 *
	 * @var Gauge
	 */
	private $_gauge;
}