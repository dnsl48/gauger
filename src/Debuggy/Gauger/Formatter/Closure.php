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
	 * @param _Closure $format Closure to be used as the formatter
	 * @param _Closure $visible Closure to be used as the isVisible
	 */
	public function __construct (_Closure $format = null, _Closure $visible = null) {
		$this->_format = $format;
		$this->_visible = $visible;
	}


	/** {@inheritdoc} */
	public function format ($value) {
		if ($this->_format)
			return call_user_func ($this->_format, $value);

		return parent::format ($value);
	}


	/** {@inheritdoc} */
	public function isVisible ($value) {
		if ($this->_visible)
			return call_user_func ($this->_visible, $value);

		return parent::isVisible ($value);
	}



	/**
	 * Instance of a closure to be used as the formatter
	 *
	 * @var _Closure
	 */
	private $_format;


	/**
	 * Instance of a closure to be used as the isVisible
	 *
	 * @var _Closure
	 */
	private $_visible;
}