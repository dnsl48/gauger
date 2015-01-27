<?php


namespace Debuggy\Gauger\Refiner;


use Debuggy\Gauger\Gauge;
use Debuggy\Gauger\Refiner;



/**
 * Root refiner that holds a Gauge and makes access to its indicators and stamps through dices
 */
class Cache extends Refiner {
	/** {@inheritdoc} */
	public function getIndicators () {
		if (!isset ($this->_cache['0']))
			$this->_cache['0'] = parent::getIndicators ();

		return $this->_cache['0'];
	}


	/** {@inheritdoc} */
	public function getStamps () {
		if (!isset ($this->_cache['1']))
			$this->_cache['1'] = parent::getStamps ();

		return $this->_cache['1'];
	}



	/**
	 * Cache of the data
	 *
	 * @var array
	 */
	private $_cache = array ();
}