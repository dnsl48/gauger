<?php


namespace Debuggy\Gauger;


use Debuggy\Gauger;
use Debuggy\Gauger\Mark;

use Debuggy\Gauger\Reporter\Formatter;


/**
 * Formats gathered info into a report
 */
abstract class Reporter {
	/**
	 * Transforms an info of gaugers list into final representation
	 *
	 * @param Gauger[] $gaugers List of Gaugers
	 *
	 * @return mixed
	 */
	abstract public function gaugers (array $gaugers);


	/**
	 * Transforms a gauger into final representation
	 *
	 * @param Gauger $gauger Gauger instance
	 *
	 * @return mixed
	 */
	abstract public function gauger (Gauger $gauger);


	/**
	 * Transforms a single Mark into final representation
	 *
	 * @param Mark $mark Mark to transform
	 *
	 * @return mixed
	 */
	abstract public function mark (Mark $mark);


	/**
	 * Transforms a list of marks into final representation
	 *
	 * @param array $marks Array of marks
	 *
	 * @return mixed
	 */
	abstract public function marks (array $marks);


	/**
	 * Set handler that will format all gauge values while reports generations
	 *
	 * @param Formatter $formatter Formatter that will transform gauges
	 *
	 * @return void
	 */
	public function setGaugeFormatter (Formatter $formatter = null) {
		$this->_gaugeFormatter = $formatter;
	}


	/**
	 * Transforms gauge value to a string
	 *
	 * @param mixed $gauge Gauge to format
	 *
	 * @return string
	 */
	public function formatGauge ($gauge) {
		return $this->_gaugeFormatter
			? $this->_gaugeFormatter->transform ($gauge)
			: (string) $gauge;
	}


	/**
	 * Gauge handler closure
	 *
	 * @var Closure
	 */
	private $_gaugeFormatter;
}