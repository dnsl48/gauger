<?php


namespace Debuggy\Gauger;


use Debuggy\Gauger;
use Debuggy\Gauger\Mark;

use Closure;


abstract class Formatter {
	/**
	 * Transform an info of gaugers list into final representation and return
	 *
	 * @param Gauger[] $gaugers List of Gaugers
	 *
	 * @return mixed
	 */
	abstract public function gaugers (array $gaugers);

	/**
	 * Transform a gauger into final representation and return
	 *
	 * @param Gauger $gauger Gauger instance
	 *
	 * @return mixed
	 */
	abstract public function gauger (Gauger $gauger);

	/**
	 * Transform a single Mark into final representation and return
	 *
	 * @param Mark $mark Mark to transform
	 *
	 * @return mixed
	 */
	abstract public function singleMark (Mark $mark);


	/**
	 * Transform a list of marks into final representation and return
	 *
	 * @param array $marks Array of marks
	 *
	 * @return mixed
	 */
	abstract public function arrayOfMarks (array $marks);


	/**
	 * Set duration handler that will format
	 * all duration values during a mark output
	 * generation
	 *
	 * @param Closure $handler Handler should take a float value and return string
	 *
	 * @return void
	 */
	public function setDurationHandler (Closure $handler = null) {
		$this->_durationHandler = $handler;
	}


	/**
	 * Transform duration float value to string for human-readable output
	 *
	 * @param float $value Duration value
	 *
	 * @return string
	 */
	public function formatDuration ($value) {
		if ($this->_durationHandler) {
			$handler = $this->_durationHandler;
			return $handler ($value);
		} else
			return number_format ($value, 6);
	}


	/**
	 * Duration handler closure
	 *
	 * @var Closure
	 */
	private $_durationHandler;
}