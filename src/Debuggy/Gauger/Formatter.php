<?php


namespace Debuggy\Gauger;


use Debuggy\Gauger;
use Debuggy\Gauger\Mark;

use Closure;


/**
 * Format gathered info into report
 */
abstract class Formatter {
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
	abstract public function singleMark (Mark $mark);


	/**
	 * Transforms a list of marks into final representation
	 *
	 * @param array $marks Array of marks
	 *
	 * @return mixed
	 */
	abstract public function arrayOfMarks (array $marks);


	/**
	 * Set handler that will format all gauge values while reports generations
	 *
	 * @param Closure $handler Handler should take a float value and return string
	 *
	 * @return void
	 */
	public function setGaugeHandler (Closure $handler = null) {
		$this->_gaugeHandler = $handler;
	}


	/**
	 * Transforms gauge value to human-readable string
	 *
	 * @param mixed $value Gauge value
	 *
	 * @return string
	 */
	public function formatGauge ($value) {
		if ($this->_gaugeHandler) {
			$handler = $this->_gaugeHandler;
			return $handler ($value);
		} else
			return number_format ($value, 6);
	}


	/**
	 * Gauge handler closure
	 *
	 * @var Closure
	 */
	private $_gaugeHandler;
}