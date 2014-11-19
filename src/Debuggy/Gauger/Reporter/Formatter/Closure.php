<?php


namespace Debuggy\Gauger\Reporter\Formatter;


use Debuggy\Gauger\Reporter\Formatter;
use Closure as _Closure;



/**
 * Holds the closure that will be invoked to transform values in appropriate representation for reports
 */
class Closure extends Formatter {
	/**
	 * Initializes the formatter by a closure that will be invoked to transform values
	 *
	 * @param _Closure $closure Closure to transform values
	 */
	public function __construct (_Closure $closure) {
		$this->_closure = $closure;
	}

	/** {@inheritdoc} */
	public function transform ($gauge) {
		$closure = $this->_closure;
		return $closure ($gauge);
	}

	/**
	 * Closure to transform values
	 *
	 * @var _Closure
	 */
	private $_closure;
}