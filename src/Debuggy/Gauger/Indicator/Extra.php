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

		if ((is_int ($former) && is_int ($latter)) || (is_float ($former) && is_float ($latter)))
			return $former + $latter;

		return $this->_merge ($former, $latter);
	}


	/** {@inheritdoc} */
	public function sub ($former, $latter) {
		if ($this->_sub)
			return call_user_func ($this->_sub, $former, $latter);

		if ((is_int ($former) && is_int ($latter)) || (is_float ($former) && is_float ($latter)))
			return $former - $latter;

		return $this->_merge ($latter, $former);
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
	 * Merges two values
	 *
	 * @param mixed $former Former parameter
	 * @param mixed $latter Latter parameter
	 *
	 * @return mixed
	 */
	private function _merge ($former, $latter) {
		if (isset ($former) xor isset ($latter))
			return isset ($former) ? $former : $latter;

		if (!isset ($former) && !isset ($latter))
			return null;

		if (is_array ($former) && is_array ($latter)) {
			$formerIsAssoc = array_sum (array_map (function ($k) {return !is_numeric ($k);}, array_keys ($former)));
			$latterIsAssoc = array_sum (array_map (function ($k) {return !is_numeric ($k);}, array_keys ($latter)));

			if ($formerIsAssoc && $latterIsAssoc) {
				if (!array_intersect_key ($former, $latter))
					return $former + $latter;
				else
					return array ($former, $latter);

			} else if (!$formerIsAssoc && $latterIsAssoc) {
				$former[] = $latter;
				return $former;

			} else if ($formerIsAssoc && !$latterIsAssoc)
				return array ($former, $latter);

			else
				return array_merge ($former, $latter);
		}

		if (is_array ($former) && !is_array ($latter)) {
			if (isset ($latter))
				$former[] = $latter;

			return $former;

		}

		return array ($former, $latter);
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