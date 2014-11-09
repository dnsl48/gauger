<?php


namespace Debuggy\Gauger\Filter;


use Debuggy\Gauger\Mark\Sequential as SequentialMark;

use Closure;


/**
 * Encapsulates closure that will be invoked for checking marks
 */
class SequentialClosure implements Sequential {
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
	public function checkSequential (SequentialMark $mark) {
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