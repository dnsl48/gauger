<?php


namespace Debuggy\Gauger\Formatter;


use Debuggy\Gauger\Formatter;

use DateTime;
use DateTimeZone;



/**
 * Formatter for time values
 */
class Time extends Formatter {
	/**
	 * Initializes the object with the precision for micro part
	 *
	 * @param int $precision Number of figures in micro part
	 */
	public function __construct ($precision = 6) {
		$this->_precision = $precision;
	}


	/** {@inheritdoc} */
	public function format ($value) {
		$time = (int) $value;
		$micro = $value - $time;
		$result = '';

		if ($time > 60 * 60 * 24) {
			$result = (int) (($time / (60 * 60 * 24))).' ';
			$time -= $result * (60 * 60 * 24);
		}

		$format = '';

		switch (true) {
			case $time >= 60 * 60:
				$format .= 'H:';

			case $time >= 60:
				$format .= 'i:';

			case $time > 0:
				$format .= 's';
				$result .= DateTime::createFromFormat ('U', $time, new DateTimeZone ('UTC'))->format ($format);
		}

		if ($micro)
			$result .= substr (number_format ($micro, $this->_precision), 1);

		return $result;
	}



	/**
	 * Number of figures in micro part
	 *
	 * @var int
	 */
	private $_precision;
}