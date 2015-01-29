<?php


namespace Debuggy\Gauger;


use Debuggy\Gauger\Presenter\Txt;

use Closure;
use Exception as BaseException;



/**
 * Sample of the Gauger components usage.
 * It also can be used as a pattern for some often situations.
 */
abstract class Sample {
	/**
	 * Initializes the object with a gauge
	 *
	 * @param Gauge $gauge Gauge instance
	 */
	public function __construct (Gauge $gauge = null) {
		if (!isset ($gauge))
			$gauge = new Gauge;

		$this->_gauge = $gauge;

		$this->initGauge ($this->_gauge);
	}


	/**
	 * Calls a gauge's method with the same name
	 *
	 * @param string $id Stamp identifier
	 * @param mixed $extra Extra data provided by a user
	 *
	 * @return void
	 */
	public function stamp ($id, $extra = null) {
		$this->getGauge ()->stamp ($id, $extra);
	}


	/**
	 * Benchmarks the subject's evaluation.
	 * If there is any exception, it will be kept in the details of a stamp and thrown forth.
	 *
	 * @param Closure $subject Subject to be benchmarked
	 * @param string $stampId Identifier for the stamps
	 * @param mixed $extra Extra data provided by users
	 * @param array $arguments Arguments for a subject's invocation
	 *
	 * @return mixed Result of the subject's invocation
	 *
	 * @throws Exteption Any exception that is thrown by the subject
	 */
	public function benchmark (Closure $subject, $stampId, $extra = null, $arguments = array ()) {
		$this->stamp ($stampId, $extra);

		try {
			$result = call_user_func_array ($subject, $arguments);

		} catch (BaseException $e) {
			$this->stamp ($stampId, array ('exception' => $e));

			throw $e;
		}

		$this->stamp ($stampId);

		return $result;
	}


	/**
	 * Returns an instance of a gauge
	 *
	 * @return Gauge
	 */
	public function getGauge () {
		return $this->_gauge;
	}


	/**
	 * Makes the recount through the Txt reporter
	 *
	 * @return string
	 */
	public function toString () {
		$presenter = new Txt;

		return $presenter->represent ($this->toArray ());
	}


	/**
	 * Returns the recount as an array
	 *
	 * @return array
	 */
	abstract public function toArray ();


	/**
	 * Initializes the $gauge by dials
	 *
	 * @param Gauge $gauge Gauge to be initialized by dials
	 *
	 * @return void
	 */
	protected function initGauge (Gauge $gauge) {}



	/**
	 * An instance of the Gauge
	 *
	 * @var Gauge
	 */
	private $_gauge;
}