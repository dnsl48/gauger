<?php


namespace Debuggy\Gauger;


/**
 * Gauges microtime stamps
 */
class StretchTimeAccumulator extends StretchAccumulator {
	/** {@inheritdoc} */
	protected function getGauge (array $details) {
		return microtime (true);
	}
}