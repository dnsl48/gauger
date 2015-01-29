<?php



namespace Debuggy\Gauger\Sample;


use Debuggy\Gauger\Dial;
use Debuggy\Gauger\Formatter;
use Debuggy\Gauger\Gauge;
use Debuggy\Gauger\Indicator;

use Closure;



/**
 * This sample contains usage of stamps' preload
 */
class Preload1 extends Sample1 {
	/**
	 * Initializes the sample with the preloads of values and their formatter.
	 * Each element of $values (and each of $otherValues sub-arrays) should be either value or pair which is array with two elements,
	 * the first of them is the key and the second is the value of a stamp.
	 *
	 * @param array $values List of preset values
	 * @param Formatter $formatter Value formatter instance
	 * @param array $otherValues List of additional values for other dials that is not a first one
	 * @param Formatter[] $otherFormatters List of formatters for additional values
	 */
	public function __construct (array $values, Formatter $formatter = null, array $otherValues = array (), array $otherFormatters = array ()) {
		$count = count ($values);
		$values = array_merge (array ($values), $otherValues);

		$formatters = array_merge (array ($formatter), $otherFormatters);

		for ($i = 0, $c = count ($values); $i < $c; ++$i) {
			$indicator = new Indicator\Preload (array_map (function ($elem) {
				return isset ($elem[0]) && isset ($elem[1]) ? $elem[1] : $elem;
			}, $values[$i]), isset ($formatters[$i]) ? $formatters[$i] : null);

			$count = min ($count, count ($values[$i]));

			$this->_dials[] = new Dial ($indicator);
		}

		parent::__construct ();

		$gauge = $this->getGauge ();

		for ($i = 0; $i < $count; ++$i)
			$gauge->stamp (isset ($values[0][$i][0]) && isset ($values[0][$i][1]) ? $values[0][$i][0] : 'preval');
	}


	/** {@inheritdoc} */
	protected function initGauge (Gauge $gauge) {
		foreach ($this->_dials as $dial)
			$gauge->addDial ($dial);

		$gauge->addDial (new Dial (new Indicator\Extra));
	}



	/**
	 * Collection of dials for a gauge initialization by
	 *
	 * @var Dial
	 */
	private $_dials = array ();
}