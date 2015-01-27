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
		if (is_scalar ($value))
			return (string) $value;

		return print_r ($value, true);
	}
}