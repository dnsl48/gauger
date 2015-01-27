<?php


namespace Debuggy\Gauger\Refiner;


use Debuggy\Gauger\Gauge;
use Debuggy\Gauger\Refiner;



/**
 * Root refiner that holds a Gauge and makes access to its indicators and stamps through dices
 */
class Root extends Refiner {
	/**
	 * Initializes the object with a gauge
	 *
	 * @param Gauge $gauge Gauge which stamps should be refined
	 */
	public function __construct (Gauge $gauge) {
		parent::__construct ($this);

		$this->_gauge = $gauge;
	}


	/** {@inheritdoc} */
	public function getIndicators () {
		$result = array ();

		foreach ($this->_gauge->getDials () as $dial)
			$result[] = $dial->getIndicator ();

		return $result;
	}


	/** {@inheritdoc} */
	public function getStamps () {
		$result = array ();

		foreach ($this->_gauge->getDials () as $dial)
			$result[] = $dial->getCollection ();

		return $result;
	}



	/**
	 * Gauge instance
	 *
	 * @var Gauge
	 */
	private $_gauge;
}