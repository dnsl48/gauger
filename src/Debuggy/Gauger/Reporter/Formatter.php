<?php


namespace Debuggy\Gauger\Reporter;


/**
 * GaugeHandler intended to format gauged values before putting them
 * into reports.
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