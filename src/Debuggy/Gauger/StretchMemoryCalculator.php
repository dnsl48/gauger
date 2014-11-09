<?php


namespace Debuggy\Gauger;


/**
 * Gauges memory usage stamps
 */
class StretchMemoryCalculator extends StretchCalculator {
	/** {@inheritdoc} */
	protected function getGauge (array $details) {
		return memory_get_usage () / 1000 / 1000;
	}
}