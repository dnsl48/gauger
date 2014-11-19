<?php


namespace Debuggy;


use Debuggy\Gauger\Reporter\Txt as TxtReporter;

use Debuggy\Gauger\Filter\Sequential as SequentialFilter;
use Debuggy\Gauger\Filter\Summary as SummaryFilter;

use Closure;
use Exception;


/**
 * Base Gauger.
 * Although it has some basic methods and constructor, its abstract methods
 * have to be implemented in derived classes. They should implement some
 * specific gauging logic.
 */
abstract class Gauger {
	/**
	 * Default name for gaugers
	 */
	const DEFAULT_NAME = 'Unnamed Gauger';


	/**
	 * Constructs instance of gauger with name
	 *
	 * @param string $name Name
	 */
	public function __construct ($name = null) {
		if (!isset ($name))
			$name = static::DEFAULT_NAME;

		$this->_name = $name;
	}


	/**
	 * Makes new mark with marker
	 *
	 * @param string $marker Marker name
	 * @param array $details Extra info about marker (optional)
	 *
	 * @return void
	 */
	abstract public function mark ($marker, $details = array ());


	/**
	 * Stamps an existent gauge with marker
	 *
	 * @param float $stamp Gauge to stamp
	 * @param string $marker Marker name
	 * @param array $details Extra info about marker (optional)
	 *
	 * @return void
	 */
	abstract public function stamp ($stamp, $marker, $details = array ());


	/**
	 * Makes new gauge and returns it
	 *
	 * @param array $details Marker details
	 *
	 * @return mixed
	 */
	abstract protected function getGauge (array $details);


	/**
	 * Returns list of marks that represents gauger info at the moment of
	 * the method invocation. If any filters passed, they will be applied to
	 * result.
	 *
	 * @param array $sequentialFilters List of SequentialFilters
	 * @param array $summaryFilters List of SummaryFilters
	 *
	 * @return Debuggy\Gauger\Mark[]
	 */
	abstract public function getMarks (array $sequentialFilters = array (), array $summaryFilters = array ());


	/**
	 * Reset all internal storages of the gauger
	 *
	 * @return void
	 */
	abstract public function reset ();


	/**
	 * Returns name of the gauger
	 *
	 * @return string
	 */
	public function getName () {
		return $this->_name;
	}


	/**
	 * Gauges the subject's evaluation.
	 * If there is any exception, it will be kept in details of a fixed mark.
	 * After it will be thrown forth.
	 *
	 * @param Closure $subject Subject to gauge
	 * @param array $arguments Arguments for subject's invocation
	 * @param string $marker Marker that denotes the subject
	 * @param array $details Extra information about marker (optional)
	 *
	 * @return mixed Result of the subject's invocation
	 *
	 * @throws Exteption Any exception that is thrown by the subject
	 */
	public function gauge (Closure $subject, $arguments = array (), $marker, $details = array ()) {
		$markers = $this->getGaugeMarkers ($marker);

		$empty = function () {};

		// Lets warmup the interpreter
		microtime (true);
		call_user_func_array ($empty, array ());

		// Lets calculate the overhead for call_user_func_array
		$overheadBefore = microtime (true);
		call_user_func_array ($empty, array ());
		$overheadAfter = microtime (true);

		// Lets minimize the overhead from call_user_func_array
		$overhead = $overheadAfter - $overheadBefore;

		$stampBefore = microtime (true);

		try {
			$result = call_user_func_array ($subject, $arguments);

		} catch (Exception $e) {
			$stampException = microtime (true) - $overhead;

			$this->stamp ($stampBefore, $markers['before'], $details);
			$details['exception'] = $e;
			$this->stamp ($stampException, $markers['exception'], $details);

			throw $e;
		}

		$stampAfter = microtime (true) - $overhead;

		$this->stamp ($stampBefore, $markers['before'], $details);
		$this->stamp ($stampAfter, $markers['after'], $details);

		return $result;
	}


	/**
	 * Set filter that will be working during the gauger's marks gathering.
	 *
	 * @param SequentialFilter $filter Filter instance
	 *
	 * @return void
	 */
	public function addFilter (SequentialFilter $filter) {
		$this->_filters[] = $filter;
	}


	/**
	 * Returns filter if it has been set
	 *
	 * @return SequentialFilter[]
	 */
	public function getFilters () {
		return $this->_filters;
	}


	/**
	 * Reset all filters
	 *
	 * @return void
	 */
	public function resetFilters () {
		$this->_filters = array ();
	}


	/**
	 * Returns a report as a string representation
	 *
	 * @return string
	 */
	public function __toString () {
		$reporter = $this->getReporter ();

		return $reporter->gauger ($this);
	}


	/**
	 * Makes and returns an instance of a reporter, most appropriate for
	 * the current gauger.
	 *
	 * @return \Debuggy\Gauger\Reporter
	 */
	protected function getReporter () {
		return new TxtReporter;
	}


	/**
	 * Returns three marks for gauging a closure, generated in order to original mark.
	 * It is using for making stamps in the method self::gauge.
	 * The array, that will be returned, contains three elements, based on
	 * originalMark: before, after, exception. Each of them will be used for passing
	 * it into this->mark method, that will be invoked from this->gauge
	 *
	 * @param string $originalMark Mark that should be used for three marks generation
	 *
	 * @return array (
	 *     'before' => 'used before closure invocation',
	 *     'after' => 'used after closure invocation',
	 *     'exception' => 'used instead of end when exception has been thrown from the closure'
	 * )
	 */
	abstract protected function getGaugeMarkers ($originalMarker);


	/**
	 * Gauger identifier
	 *
	 * @var string
	 */
	private $_name;


	/**
	 * SequentialFilters' storage
	 *
	 * @var SequentialFilter
	 */
	private $_filters = array ();



	/** STATIC AREA **/


	/**
	 * Returns a gauger by name. If it hasn't been created yet, it's going to be.
	 * Furthermore, it's going to be kept in tha static scope of the class.
	 * The next invocations of static::getStatic will always return the same object
	 * that has been created in the first time.
	 *
	 * @param string $name Name of a gauger
	 *
	 * @return static
	 */
	public static function getStatic ($name = null) {
		if (!isset (self::$_gaugers[$name]))
			self::$_gaugers[$name] = new static ($name);

		return self::$_gaugers[$name];
	}

	/**
	 * Map of gaugers that were created with static::getStatic method
	 *
	 * @var array
	 */
	private static $_gaugers = array ();
}