<?php


namespace Debuggy\Gauger\Filter;


use Debuggy\Gauger\Filter;
use Debuggy\Gauger\Stamp;



/**
 * Returns only stamps with distinct values
 */
class Distinct extends Filter {
	/**
	 * Initializes the object with the decision about strictness.
	 * If it's strict, the values will be compared strictly (with the operator "===").
	 *
	 * @param bool $strict Strictness of the filter
	 */
	public function __construct ($strict = true) {
		$this->_strict = $strict;
	}


	/** {@inheritdoc} */
	public function checkStamp (Stamp $stamp) {
		return true; // any single value is always distinct
	}


	/** {@inheritdoc} */
	public function checkBunch (array $stamps) {
		$result = array ();
		$valuesBuffer = array ();

		for ($i = 0, $c = count ($stamps); $i < $c; ++$i) {
			$value = $stamps[$i]->value;

			if (array_search ($value, $valuesBuffer, $this->_strict) === false) {
				$result[] = $stamps[$i];
				$valuesBuffer[] = $value;
			}
		}

		return $result;
	}



	/**
	 * Whether comparison to be strict
	 *
	 * @var bool
	 */
	private $_strict;
}