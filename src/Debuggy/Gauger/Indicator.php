<?php


namespace Debuggy\Gauger;



/**
 * Indicator should implement some logic of harvesting data
 */
abstract class Indicator {
	/**
	 * Initializes the object with a formatter
	 *
	 * @param Formatter $formatter Formatter for the indicator's values
	 */
	public function __construct (Formatter $formatter = null) {
		if (!isset ($formatter))
			$formatter = new Formatter;

		$this->_formatter = $formatter;
	}


	/**
	 * Returns the name of the indicator
	 *
	 * @return string
	 */
	public function getName () {
		return substr (get_class ($this), ($pos = strrpos (get_class ($this), '\\')) + ($pos ? 1 : 0));
	}


	/**
	 * Summarizes two values and produces the third
	 *
	 * @param mixed $former Former value
	 * @param mixed $latter Latter value
	 *
	 * @return mixed
	 */
	public function sum ($former, $latter) {
		if (is_numeric ($former) && is_numeric ($latter))
			return $former + $latter;
		else
			return ((string) $former) . ' + ' . ((string) $latter);
	}


	/**
	 * Subtracts the latter value from the former one and returns the result
	 *
	 * @param mixed $former Former value
	 * @param mixed $latter Latter value
	 *
	 * @return mixed
	 */
	public function sub ($former, $latter) {
		if (is_numeric ($former) && is_numeric ($latter))
			return $former - $latter;
		else
			return ((string) $former) . ' - ' . ((string) $latter);
	}


	/**
	 * Figures out the average value from the list of values
	 *
	 * @param array $values List of values
	 *
	 * @return mixed
	 */
	public function avg (array $values) {
		if (!count ($values))
			return null;

		$_this = $this;

		$sum = array_reduce (array_slice ($values, 1), function ($sum, $value) use ($_this) {
			return $_this->sum ($sum, $value);
		}, $values[0]);

		if (is_numeric ($sum))
			$avg = $sum / count ($values);

		else
			$avg = $sum . ' / ' . count ($values);

		return $avg;
	}


	/**
	 * Returns the formatter of the indicator
	 *
	 * @return Formatter
	 */
	public function getFormatter () {
		return $this->_formatter;
	}


	/**
	 * Should be called each time when the result of the indicator's gauge is not gonna be used
	 *
	 * @return void
	 */
	public function idle () {}


	/**
	 * Returns the data, harvested by the indicator
	 *
	 * @return mixed
	 */
	abstract public function gauge ();



	/**
	 * Formatter for the values
	 *
	 * @var Formatter
	 */
	private $_formatter;
}