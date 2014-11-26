<?php


namespace Debuggy\Gauger;


use Closure;


/**
 * Calls closure to make the gauge
 */
class StretchClosureAccumulator extends StretchAccumulator {
	/**
	 * Initializes the accumulator with name and closure.
	 * The closure will be used to gauge.
	 *
	 * @param Closure $closure Closure to gauge
	 * @param string $name Name of the gauger
	 */
	public function __construct (Closure $closure, $name = null) {
		$this->_closure = $closure;
	}


	/** {@inheritdoc} */
	protected function getGauge (array $extra) {
		$closure = $this->_closure;

		return $closure ($extra);
	}


	/**
	 * Closure that will be invoked to gauge a marker
	 *
	 * @var Closure
	 */
	private $_closure;
}