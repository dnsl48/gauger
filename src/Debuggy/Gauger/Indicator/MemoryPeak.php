<?php


namespace Debuggy\Gauger\Indicator;



/**
 * Shows the peak usage of memory by the interpretator
 */
class MemoryPeak extends Memory {
	/** {@inheritdoc} */
	public function gauge ($extra = null) {
		return memory_get_peak_usage ();
	}
}