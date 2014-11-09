<?php


namespace Debuggy\Gauger\Filter;


use Debuggy\Gauger\Mark\Summary as SummaryMark;

use Closure;


/**
 * Encapsulates closure that will be invoked for checking marks
 */
class SummaryClosure implements Summary {
	/**
	 * Takes closure that should be invoked for checking marks
	 *
	 * @param Closure $closure Closure that will check the marks
	 */
	public function __construct (Closure $closure) {
		$this->_closure = $closure;
	}

	/**
	 * {@inheritdoc}
	 */
	public function checkSummary (SummaryMark $mark) {
		$closure = $this->_closure;
		return $closure ($mark);
	}

	/**
	 * Closure for checking marks
	 *
	 * @var Closure
	 */
	private $_closure;
}