<?php


namespace Debuggy\Gauger\Filter;


use Debuggy\Gauger\Mark\Summary as SummaryMark;

use Closure;


/**
 * Encapsulates closure that will be invoked each time for checking a mark
 */
class SummaryClosure implements Summary {
	/**
	 * Takes closure that should be invoked for each mark that should be checked
	 *
	 * @param Closure $closure Closure that will be invoked for each mark
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
	 * Closure for check marks
	 *
	 * @var Closure
	 */
	private $_closure;
}