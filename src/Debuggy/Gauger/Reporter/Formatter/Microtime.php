<?php


namespace Debuggy\Gauger\Reporter\Formatter;


use Debuggy\Gauger\Reporter\Formatter;



/**
 * The gauge should be a count of microseconds.
 * Result will be seconds with microseconds (1.000006 equals 1 second and 6 microseconds)
 */
class Microtime implements Formatter {
	/**
	 * Initializes precision for seconds
	 *
	 * @param int $precision Amount of floating precision
	 */
	public function __construct ($precision = 6) {
		$this->_precision = $precision;
	}


	/** {@inheritdoc} */
	public function transform ($gauge) {
		return number_format ($gauge, $this->_precision);
	}


	/**
	 * Precision for second
	 *
	 * @var int
	 */
	private $_precision;
}