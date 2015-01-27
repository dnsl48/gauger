<?php


namespace Debuggy\Gauger\Filter;


use Debuggy\Gauger\Filter;
use Debuggy\Gauger\Stamp;



/**
 * Filters only values equal to the value of the filter
 */
class Equal extends Filter {
	/**
	 * Initializes the object with the value and the strictness
	 *
	 * @param mixed $value Value for filtering
	 * @param bool $strict Whether the equation should be strict
	 */
	public function __construct ($value, $strict = true) {
		$this->_value = $value;
		$this->_strict = $strict;
	}


	/** {@include} */
	public function checkStamp (Stamp $stamp) {
		if ($this->_strict)
			return $stamp->value === $this->_value;
		else
			return $stamp->value == $this->_value;
	}



	/**
	 * Value for the equation
	 *
	 * @var mixed
	 */
	private $_value;


	/**
	 * Whether the equation should be strict
	 *
	 * @var bool
	 */
	private $_strict;
}
