<?php


namespace Debuggy\Gauger\Formatter;


use Debuggy\Gauger\Formatter;

use Closure as _Closure;



/**
 * Provides the ability to apply a closure to values as a formatter
 */
class Closure extends Formatter {
	/**
	 * Initializes the object with the closure to be used as the formatter
	 *
	 * @param _Closure $closure Closure to be used as the formatter
	 */
	public function __construct (_Closure $closure) {
		$this->_closure = $closure;
	}


	/** {@inheritdoc} */
	public function format ($value) {
		$closure = $this->_closure;
		return $closure ($value);
	}



	/**
	 * Instance of a closure to be used as the formatter
	 *
	 * @var _Closure
	 */
	private $_closure;
}