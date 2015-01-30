<?php


namespace Debuggy\Gauger\Filter;


use Debuggy\Gauger\Filter;
use Debuggy\Gauger\Stamp;



/**
 * Filters the amount of last stamps
 */
class Last extends Filter {
	/**
	 * Initializes the object with the number that indicates how many values to be returned
	 * as a result of checkBunch
	 *
	 * @param int $limit Number of the values to be returned from the checkBunch
	 */
	public function __construct ($limit = 1) {
		$this->_limit = abs ($limit);
	}


	/** {@inheritdoc} */
	public function checkStamp (Stamp $stamp) {
		return $this->_limit > 0;
	}


	/** {@inheritdoc} */
	public function checkBunch (array $stamps) {
		return array_slice ($stamps, -$this->_limit);
	}



	/**
	 * That number of stamps to be returned from the checkBunch
	 *
	 * @var int
	 */
	private $_limit;
}