<?php


namespace Debuggy\Gauger\Filter;


use Debuggy\Gauger\Mark;
use Debuggy\Gauger\Mark\Sequential as SequentialMark;
use Debuggy\Gauger\Mark\Summary as SummaryMark;


/**
 * Filter for marks by their duration
 */
class Duration implements Sequential, Summary {
	/**
	 * Setup minimum and maximum duration for a mark to pass the checking
	 *
	 * @param int $min Minimum value
	 * @param int $max Maximum value
	 */
	public function __construct ($min, $max = null) {
		$this->_min = $min;
		$this->_max = $max;
	}

	/**
	 * {@inheritdoc}
	 */
	public function checkSequential (SequentialMark $mark) {
		return $this->checkMark ($mark);
	}


	/**
	 * {@inheritdoc}
	 */
	public function checkSummary (SummaryMark $mark) {
		return $this->checkMark ($mark);
	}


	/**
	 * {@inheritdoc}
	 */
	public function checkMark (Mark $mark) {
		return
			(!isset ($this->_min) || $mark->duration >= $this->_min)
		&&
			(!isset ($this->_max) || $mark->duration <= $this->_max);
	}
}