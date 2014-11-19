<?php


namespace Debuggy\Gauger\Reporter;


/**
 * GaugeHandler intended to format gauged values before putting them
 * into reports.
 */
abstract class Formatter {
	/**
	 * Transforms the value to a string
	 *
	 * @param mixed $value Value for transformation
	 *
	 * @return string
	 */
	abstract public function transform ($value);
}