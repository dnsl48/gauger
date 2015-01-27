<?php


namespace Debuggy\Gauger\Indicator;


use Debuggy\Gauger\Formatter;
use Debuggy\Gauger\Indicator;



/**
 * Returns preloaded values
 */
class Preload extends Indicator {
	/**
	 * Initializes the object with array of values
	 *
	 * @param array $values Values for the object preloading
	 * @param Formatter $formatter Formatter for indicator values
	 */
	public function __construct (array $values, Formatter $formatter = null) {
		parent::__construct ($formatter);

		$this->_values = $values;
	}


	/** {@inheritdoc} */
	public function gauge () {
		list ($key, $val) = each ($this->_values);
		return $val;
	}


	/** {@inheritdoc} */
	public function idle () {
		$this->gauge ();
	}



	/**
	 * Preload of values
	 *
	 * @var array
	 */
	private $_values;
}