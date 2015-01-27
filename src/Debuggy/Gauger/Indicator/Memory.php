<?php


namespace Debuggy\Gauger\Indicator;


use Debuggy\Gauger\Formatter;
use Debuggy\Gauger\Formatter\Memory as MemoryFormatter;
use Debuggy\Gauger\Indicator;



/**
 * Base for indicators of memory
 */
abstract class Memory extends Indicator {
	/** {@inheritdoc} */
	public function __construct (Formatter $formatter = null) {
		if (!isset ($formatter))
			$formatter = new MemoryFormatter;

		parent::__construct ($formatter);
	}
}