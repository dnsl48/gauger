<?php


namespace Debuggy\Gauger\Filter;


use Debuggy\Gauger\Filter;
use Debuggy\Gauger\Stamp;



/**
 * Omits a number of first values and returns all others
 */
class Tail extends Filter {
	/**
	 * Initializes the object with the number that indicates how many stamps to be omitted
	 *
	 * @param int $offset Number of stamps to be omitted
	 */
	public function __construct ($offset = 1) {
		$this->_offset = abs ($offset);
	}


	/** {@inheritdoc} */
	public function checkStamp (Stamp $stamp) {
		return $this->_counter++ >= $this->_offset;
	}


	/** {@inheritdoc} */
	public function checkBunch (array $stamps) {
		return array_slice ($stamps, $this->_offset);
	}



	/**
	 * Counter of returned stamps
	 *
	 * @var int
	 */
	private $_counter = 0;


	/**
	 * Amount of stamps to be omitted
	 *
	 * @var int
	 */
	private $_offset;
}