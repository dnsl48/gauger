<?php


namespace Debuggy\Gauger\Filter;


use Debuggy\Gauger\Mark\Sequential as SequentialMark;


/**
 * Filter for sequential marks
 */
interface Sequential {
	/**
	 * Check a sequential mark and return result whether it should be
	 * stored for result set of marks
	 *
	 * @param SequentialMark $mark Mark for checking
	 *
	 * @return bool
	 */
	public function checkSequential (SequentialMark $mark);
}