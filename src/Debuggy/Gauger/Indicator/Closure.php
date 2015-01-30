<?php


namespace Debuggy\Gauger\Indicator;


use Debuggy\Gauger\Formatter;
use Debuggy\Gauger\Indicator;

use Closure as _Closure;



/**
 * Incapsulates user-defined closure for producing data
 */
class Closure extends Indicator {
	/**
	 * Initializes the object with the user-defined indicator
	 *
	 * @param \Closure $indicator User-defined indicator
	 * @param \Closure $formatter Formatter for the data
	 */
	public function __construct (_Closure $indicator, Formatter $formatter = null) {
		parent::__construct ($formatter);

		$this->_indicator = $indicator;
	}


	/** {@inheritdoc} */
	public function gauge ($extra = null) {
		$closure = $this->_indicator;

		return $closure ($extra);
	}



	/**
	 * Closure for data producing
	 *
	 * @var \Closure
	 */
	private $_indicator;
}