<?php


namespace Debuggy\Gauger;


use Debuggy\Gauger\Reporter\Formatter\Microtime as MicrotimeFormatter;


/**
 * Gauges microtime stamps
 */
class StretchTimeAccumulator extends StretchAccumulator {
	/** {@inheritdoc} */
	protected function getGauge (array $details) {
		return microtime (true);
	}


	/** {@inheritdoc} */
	protected function getReporter () {
		$reporter = parent::getReporter ();

		$reporter->setGaugeFormatter (new MicrotimeFormatter);

		return $reporter;
	}
}