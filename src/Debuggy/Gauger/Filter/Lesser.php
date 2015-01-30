<?php


namespace Debuggy\Gauger\Filter;



/**
 * Filters only values lesser than the value of the filter
 */
class Lesser extends Between {
	/**
	 * Initializes the object with the value
	 *
	 * @param mixed $value Value for filtering
	 * @param bool|null $bcMath Whether to use BCMath library
	 */
	public function __construct ($value, $bcMath = false) {
		parent::__construct ($value, null, true, $bcMath);
	}
}
