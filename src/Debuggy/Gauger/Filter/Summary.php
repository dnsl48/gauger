<?php


namespace Debuggy\Gauger\Filter;


use Debuggy\Gauger\Mark\Summary as SummaryMark;


/**
 * Filter for summary marks
 */
interface Summary {
	/**
	 * Check a summary mark and return result whether it should be
	 * stored for result set of marks
	 *
	 * @param SummaryMark $mark Mark for checking
	 *
	 * @return bool
	 */
	public function checkSummary (SummaryMark $mark);
}