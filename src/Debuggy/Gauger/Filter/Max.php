<?php


namespace Debuggy\Gauger\Filter;


use Debuggy\Gauger\Filter;
use Debuggy\Gauger\Stamp;



/**
 * Filters a number of stamps with the maximal values
 */
class Max extends Filter {
	/**
	 * Initializes the object with a number that indicates how many values to be returned
	 * as a result of the checkBunch
	 *
	 * @param int $limit Limit of stamps to be returned
	 */
	public function __construct ($limit = 1) {
		$this->_limit = abs ($limit);
	}


	/** {@inheritdoc} */
	public function checkStamp (Stamp $stamp) {
		return $this->_limit > 0; // any single stamp has its maximal value
	}


	/** {@inheritdoc} */
	public function checkBunch (array $stamps) {
		$result = array ();

		for ($i = 0, $c = count ($stamps); $i < $c; ++$i)
			$result[$stamps[$i]->value] = $stamps[$i];

		ksort ($result);

		return array_values (array_slice ($result, -$this->_limit));
	}



	/**
	 * The number of stamps to be returned from the checkBunch
	 *
	 * @var int
	 */
	private $_limit;
}