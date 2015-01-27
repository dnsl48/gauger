<?php


namespace Debuggy\Gauger\Indicator;



/**
 * Shows the usage of memory by the interpretator
 */
class MemoryUsage extends Memory {
	/** {@inheritdoc} */
	public function gauge () {
		return memory_get_usage ();
	}
}