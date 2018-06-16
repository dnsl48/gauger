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
		$this->_pointer = 0;
	}


	/** {@inheritdoc} */
	public function gauge ($extra = null) {
		if ($this->_pointer >= count($this->_values)) {
			return null;
		}

		return $this->_values[$this->_pointer++];
	}


	/** {@inheritdoc} */
	public function idle ($extra = null) {
		$this->gauge ($extra);
	}


	/**
	 * Preload of values
	 *
	 * @var array
	 */
	private $_values;

	/**
	 * Array pointer
	 */
	private $_pointer;
}
