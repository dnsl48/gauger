<?php


namespace Debuggy\Gauger;


/**
 * Gauges microtime stamps
 */
class StretchTimeCalculator extends StretchCalculator {
	/** {@inheritdoc} */
	protected function getGauge (array $details) {
		return microtime (true);
	}
}