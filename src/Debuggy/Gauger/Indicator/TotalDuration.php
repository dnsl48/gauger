<?php


namespace Debuggy\Gauger\Indicator;


use Debuggy\Gauger\Formatter;



/**
 * Returns total duration of the script. Relies on _SERVER['REQUEST_TIME_FLOAT']
 */
class TotalDuration extends Microtime {
	/** {@inheritdoc} */
	public function __construct (Formatter $formatter = null) {
		parent::__construct ($formatter);

		if (isset ($_SERVER['REQUEST_TIME_FLOAT']))
			$this->_timePoint = $_SERVER['REQUEST_TIME_FLOAT'];

		else if (isset ($_SERVER['REQUEST_TIME']))
			$this->_timePoint = $_SERVER['REQUEST_TIME'];

		else
			$this->_timePoint = microtime (true);
	}


	/** {@inheritdoc} */
	public function gauge () {
		return microtime (true) - $this->_timePoint;
	}



	/**
	 * Point of the time when the script started
	 *
	 * @var float
	 */
	private $_timePoint;
}