<?php


namespace Debuggy\Gauger\Filter;


use Debuggy\Gauger\Mark\Sequential as SequentialMark;

use Closure;


/**
 * Encapsulates closure that will be invoked each time for checking a mark
 */
class SequentialClosure implements Sequential {
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
	public function checkSequential (SequentialMark $mark) {
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