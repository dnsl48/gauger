<?php


namespace Debuggy\Gauger;



/**
 * Converts an object into a string
 */
class Formatter {
	/**
	 * Makes a string from the $value and returns it
	 *
	 * @param mixed $value Value to be converted into a string
	 *
	 * @return string
	 */
	public function format ($value) {
		if (is_bool ($value))
			return $value ? "true" : "false";

		else if (is_scalar ($value))
			return (string) $value;

		foreach ($value as $key => $val)
			$value[$key] = $this->format ($val);

		return $value;
	}


	/**
	 * Checks whether the value should be showed
	 *
	 * @param mixed $value Value to be checked
	 *
	 * @return bool
	 */
	public function isVisible ($value) {
		return isset ($value);
	}
}