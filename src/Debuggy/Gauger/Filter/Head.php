<?php


namespace Debuggy\Gauger\Filter;


use Debuggy\Gauger\Filter;
use Debuggy\Gauger\Stamp;



/**
 * Returns a bunch of the first values
 */
class Head extends Filter {
	/**
	 * Initializes the object with the number that indicates how many values to be returned
	 * as a result of checkBunch
	 *
	 * @param int $limit Limit of a result for the checkBunch
	 */
	public function __construct ($limit = 1) {
		$this->_limit = abs ($limit);
	}


	/** {@inheritdoc} */
	public function checkStamp (Stamp $stamp) {
		return $this->_limit > $this->_counter++;
	}


	/** {@inheritdoc} */
	public function checkBunch (array $stamps) {
		return array_slice ($stamps, 0, $this->_limit);
	}



	/**
	 * Counter of the returned stamps for singlular checks
	 *
	 * @var int
	 */
	private $_counter = 0;


	/**
	 * The number of stamps to be returned from the checkBunch
	 *
	 * @var int
	 */
	private $_limit;
}