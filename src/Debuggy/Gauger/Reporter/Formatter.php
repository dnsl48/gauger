<?php


namespace Debuggy\Gauger\Reporter;


/**
 * Intended to format values before putting them into reports.
 */
interface Formatter {
	/**
	 * Transforms the value to a string
	 *
	 * @param mixed $value Value for transformation
	 *
	 * @return string
	 */
	public function transform ($value);
}