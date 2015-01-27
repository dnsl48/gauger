<?php


namespace Debuggy\Gauger\Filter;


use Debuggy\Gauger\Filter;
use Debuggy\Gauger\Stamp;



/**
 * Allows to filter numerical values
 */
class Between extends Filter {
	/**
	 * Initializes object by numerical range. If some arguments are null
	 * it means the range doesn't have bounds on that side. Minimal and maximal
	 * values are embraced into the range.
	 * The third parameter means that the value shouldn't be in the range to pass the check.
	 * The fourth parameter sets up whether the BCMath library to be used for comparing the values.
	 * If the fourth argument it is null, it'll be figured out automatically whether the BCMath is available.
	 *
	 * @param mixed $min Minimal value
	 * @param mixed $max Maximal value
	 * @param bool $inversion Flag of inversion
	 * @param bool $bcMath Whether to use libbcmath
	 */
	public function __construct ($min = null, $max = null, $inversion = false, $bcMath = false) {
		$this->_inversion = $inversion;

		if (is_null ($bcMath) && function_exists ('bccomp')) {
			$bcMath = true;

			$minScale = 0;
			$maxScale = 0;

			if ($pos = strpos ('.', $min) !== false)
				$minScale = strlen ($min) - $pos;

			if ($pos = strpos ('.', $max) !== false)
				$maxScale = strlen ($max) - $pos;

			$this->_bcMathScale = max ($minScale, $maxScale) + 1;
		}

		$this->_bcMath = $bcMath;

		if (isset ($min)) {
			if ($this->_bcMath)
				$this->_min = strval ($min);

			else if (is_int ($min))
				$this->_min = $min;

			else
				$this->_min = floatval ($min);
		}

		if (isset ($max)) {
			if ($this->_bcMath)
				$this->_max = strval ($max);

			else if (is_int ($max))
				$this->_max = $max;

			else
				$this->_max = floatval ($max);
		}
	}


	/** {@inheritdoc} */
	public function checkStamp (Stamp $stamp) {
		if (is_null ($stamp->value))
			return false;

		if (isset ($this->_min)) {
			if ($this->_bcMath) {
				if (bccomp ($this->_min, strval ($stamp->value), $this->_bcMathScale) > 0)
					return boolval (false xor $this->_inversion);

			} else if ($this->_min > $stamp->value)
				return boolval (false xor $this->_inversion);
		}

		if (isset ($this->_max)) {
			if ($this->_bcMath) {
				if (bccomp (strval ($stamp->value), $this->_max, $this->_bcMathScale) > 0)
					return boolval (false xor $this->_inversion);

			} else if ($this->_max < $stamp->value)
				return boolval (false xor $this->_inversion);
		}

		return boolval (true xor $this->_inversion);
	}



	/**
	 * Minimal value
	 *
	 * @var mixed
	 */
	private $_min;


	/**
	 * Maximal value
	 *
	 * @var mixed
	 */
	private $_max;


	/**
	 * Whether the result should be inversed
	 *
	 * @var bool
	 */
	private $_inversion;


	/**
	 * Whether to use libbcmath for precise math
	 *
	 * @var bool
	 */
	private $_bcMath;


	/**
	 * Scale to use in bcmath functions
	 *
	 * @var int
	 */
	private $_bcMathScale = 0;

}