<?php


namespace Debuggy\Gauger\Indicator;


use Debuggy\Gauger\Formatter;
use Debuggy\Gauger\Indicator;

use Closure as _Closure;



/**
 * Harvests values from extras
 */
class Extra extends Indicator {
	/**
	 * Initializes the object with a key of extra where values should be looked for.
	 * If key is null, full extra is decided to be a value.
	 *
	 * @param mixed $key Key for searching values in extras
	 * @param Formatter $formatter Formatter for indicator values
	 */
	public function __construct ($key = null, Formatter $formatter = null, _Closure $sum = null, _Closure $sub = null, _Closure $avg = null) {
		parent::__construct ($formatter);

		$this->_key = $key;
		$this->_sum = $sum;
		$this->_sub = $sub;
		$this->_avg = $avg;
	}


	/** {@inheritdoc} */
	public function gauge ($extra = null) {
		if (is_null ($this->_key))
			return $extra;

		else if (is_array ($extra) && isset ($extra[$this->_key]))
			return $extra[$this->_key];

		else
			return null;
	}


	/** {@inheritdoc} */
	public function sum ($former, $latter) {
		if ($this->_sum)
			return call_user_func ($this->_sum, $former, $latter);

		if (is_array ($former) && !is_array ($latter)) {
			if (isset ($latter))
				$former[] = $latter;

			return $former;

		} else if (is_array ($former) && is_array ($latter)) {
			if (!isset ($former[0]))
				return array ($former, $latter);

			else if (!isset ($latter[0])) {
				$former[] = $latter;
				return $former;

			} else
				return array_merge ($former, $latter);

		} else if (!isset ($former) && is_array ($latter)) {
			return $latter;

		} else if (!isset ($former) && !isset ($latter)) {
			return null;

		} else {
			if ((is_int ($former) && is_int ($latter)) || (is_float ($former) && is_float ($latter)))
				return $former + $latter;

			$result = array ();

			if (isset ($former))
				$result[] = $former;

			if (isset ($latter))
				$result[] = $latter;

			return $result ? $result : null;
		}
	}


	/** {@inheritdoc} */
	public function sub ($former, $latter) {
		if ($this->_sub)
			return call_user_func ($this->_sub, $former, $latter);

		if ((is_int ($former) && is_int ($latter)) || (is_float ($former) && is_float ($latter)))
			return $former - $latter;

		$r = array ();

		if (isset ($former))
			$r['f'] = $former;

		if (isset ($latter))
			$r['l'] = $latter;

		return $r ? $r : null;
	}


	/** {@inheritdoc} */
	public function avg (array $values) {
		if ($this->_avg)
			return call_user_func ($this->_avg, $values);

		if ($values) {
			// check each value is numeric
			$flag = true;
			for ($i = 0, $c = count ($values); $i < $c; ++$i) {
				if (!is_int ($values[$i]) && !is_float ($values[$i])) {
					$flag = false;
					break;
				}
			}

			if ($flag)
				return parent::avg ($values);
		}

		return null;
	}



	/**
	 * Key for searching values in extras
	 *
	 * @var null|string
	 */
	private $_key;


	/**
	 * _Closure to sum values
	 *
	 * @var null|_Closure
	 */
	private $_sum;


	/**
	 * _Closure to sub values
	 *
	 * @var null|_Closure
	 */
	private $_sub;


	/**
	 * _Closure to avg values
	 *
	 * @var null|_Closure
	 */
	private $_avg;
}