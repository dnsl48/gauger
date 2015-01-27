<?php


namespace Debuggy\Gauger\Filter;


use Debuggy\Gauger\Filter;
use Debuggy\Gauger\Stamp;



/**
 * Checks by the list of filters. If any filter returns true, the result will be true
 */
class OrFilters extends Filter {
	/**
	 * Initializes the object by the the list of filters
	 *
	 * @param Filter[] $filters List of filters
	 */
	public function __construct (array $filters) {
		$this->_filters = $filters;
	}


	/** {@inheritdoc} */
	public function checkStamp (Stamp $stamp) {
		for ($i = 0, $c = count ($this->_filters); $i < $c; ++$i) {
			if ($this->_filters[$i]->checkStamp ($stamp))
				return true;
		}

		return false;
	}



	/**
	 * List of the filters for checks performing
	 *
	 * @var Filter[]
	 */
	private $_filters;
}