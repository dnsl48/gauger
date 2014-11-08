<?php


namespace Debuggy\Gauger\Filter;


use Debuggy\Gauger\Mark\Sequential as SequentialMark;


/**
 * Always returns false for all marks
 */
class SequentialFalse implements Sequential {
	/** {@inheritdoc} */
	public function checkSequential (SequentialMark $mark) {
		return false;
	}
}