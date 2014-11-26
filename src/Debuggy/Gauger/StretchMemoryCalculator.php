<?php


namespace Debuggy\Gauger;


use Debuggy\Gauger\Reporter\Formatter\Memory as MemoryFormatter;


/**
 * Gauges memory usage stamps
 */
class StretchMemoryCalculator extends StretchCalculator {
	/** {@inheritdoc} */
	protected function getGauge (array $extra) {
		return memory_get_usage ();
	}


	/** {@inheritdoc} */
	protected function getReporter () {
		$reporter = parent::getReporter ();

		$reporter->setGaugeFormatter (new MemoryFormatter);

		return $reporter;
	}
}