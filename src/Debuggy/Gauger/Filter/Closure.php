<?php


namespace Debuggy\Gauger\Filter;


use Debuggy\Gauger\Filter;
use Debuggy\Gauger\Stamp;

use Closure as _Closure;


/**
 * Provides an ability to apply a user-defined closure to a Stamp for an evaluation
 */
class Closure extends Filter {
	/**
	 * Initializes the object with the closures.
	 * The second argument is optional, however if it's passed it'll be used for check
	 * a collection of stamps instead of invocation the first closure for each one.
	 *
	 * @param \Closure $single Closure(Stamp $stamp) for an evaluation of each stamp
	 * @param \Closure $bunch Closure(Stamp[] $stamps) for an evaluation of a list of stamps
	 */
	public function __construct (_Closure $single = null, _Closure $bunch = null) {
		$this->_single = $single;
		$this->_bunch = $bunch;
	}


	/** {@inheritdoc} */
	public function checkStamp (Stamp $stamp) {
		if (!$this->_single)
			return true;

		$closure = $this->_single;

		return $closure ($stamp);
	}


	/** {@inheritdoc} */
	public function checkBunch (array $stamps) {
		if (!$this->_bunch)
			return parent::checkBunch ($stamps);

		$closure = $this->_bunch;

		return $closure ($stamps);
	}



	/**
	 * Closure to be applied to each stamp
	 *
	 * @var \Closure
	 */
	private $_single;


	/**
	 * Closure to be applied to a list of stamps
	 *
	 * @var \Closure
	 */
	private $_bunch;

}