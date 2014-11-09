<?php


namespace Debuggy\Gauger\Filter;


use Debuggy\Gauger\Mark;
use Debuggy\Gauger\Mark\Sequential as SequentialMark;
use Debuggy\Gauger\Mark\Summary as SummaryMark;


/**
 * Implements filtering logic by ranges of gauges
 */
class Between implements Sequential, Summary {
	/**
	 * Setup minimum and maximum points. The range between them
	 * will be pass the filter.
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
	 * Check that mark's gauge is greater than min and lesser than max
	 *
	 * @param Mark $mark Mark for checking
	 */
	public function checkMark (Mark $mark) {
		return
			(!isset ($this->_min) || $mark->gauge >= $this->_min)
		&&
			(!isset ($this->_max) || $mark->gauge <= $this->_max);
	}
}