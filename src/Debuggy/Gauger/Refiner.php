<?php


namespace Debuggy\Gauger;



/**
 * Base refiner with base method's implementations
 */
class Refiner {
	/**
	 * Initializes the object with a refiner
	 *
	 * @param Refiner $refiner Refiner which data should be cached
	 */
	public function __construct (Refiner $refiner) {
		$this->_refiner = $refiner;
	}


	/**
	 * Returns the list of indicators
	 *
	 * @return \Debuggy\Gauger\Indicator
	 */
	public function getIndicators () {
		return $this->_refiner->getIndicators ();
	}


	/**
	 * Returns the list of collections of stamps harvested by the indicators
	 *
	 * @return array
	 */
	public function getStamps () {
		return $this->_refiner->getStamps ();
	}



	/**
	 * Source refiner instance
	 *
	 * @var Refiner
	 */
	private $_refiner;
}