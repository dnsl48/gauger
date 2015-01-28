<?php


namespace Debuggy\Gauger\Indicator;


use Debuggy\Gauger\Formatter;
use Debuggy\Gauger\Formatter\Time;
use Debuggy\Gauger\Indicator;



/**
 * Producer of the current microtime
 */
class Microtime extends Indicator {
	/** {@inheritdoc} */
	public function __construct (Formatter $formatter = null) {
		if (!isset ($formatter))
			$formatter = new Time;

		parent::__construct ($formatter);
	}

	/**
	 * Returns the current microtime
	 *
	 * @return float
	 */
	public function gauge ($extra = null) {
		return microtime (true);
	}
}