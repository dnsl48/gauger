<?php


namespace Debuggy;


use Debuggy\Gauger\Formatter\Txt as TxtFormatter;

use Debuggy\Gauger\Filter\Sequential as SequentialFilter;
use Debuggy\Gauger\Filter\Summary as SummaryFilter;

use Closure;
use Exception;


/**
 * Base Gauger.
 * Although it has some basic methods and constructor, its other methods
 * have to be implemented in derived classes, that are to implement some
 * specific gauging logic.
 */
abstract class Gauger {
	/**
	 * A name of the default gauger
	 */
	const DEFAULT_NAME = 'Unnamed Gauger';


	/**
	 * Makes a gauger with identifier
	 *
	 * @param string $name Identifier
	 */
	public function __construct ($name = null) {
		if (!isset ($name))
			$name = static::DEFAULT_NAME;

		$this->_name = $name;
	}


	/**
	 * Makes new stamp with marker
	 *
	 * @param string $marker Marker that denotes the stamp
	 * @param array $details Note for that stamp (optional)
	 *
	 * @return void
	 */
	abstract public function mark ($marker, $details = array ());


	/**
	 * Registers an existent stamp with marker
	 *
	 * @param float $stamp Microtime stamp
	 * @param string $marker Marker that denotes the stamp
	 * @param array $details Note for that stamp (optional)
	 *
	 * @return void
	 */
	abstract public function stamp ($stamp, $marker, $details = array ());


	/**
	 * Returns list of marks that represents gauger info at the moment of
	 * this method invocation. If any filters passed, it'll be applied to
	 * result set only, so that they shouldn't affect any durations.
	 *
	 * @param array $sequentialFilters List of SequentialFilters
	 * @param array $summaryFilters List of SummaryFilters
	 *
	 * @return Debuggy\Gauger\Mark[]
	 */
	abstract public function getMarks (array $sequentialFilters = array (), array $summaryFilters = array ());


	/**
	 * Resets all internal storages of the instance so it is going to clear
	 * all collected data.
	 *
	 * @return void
	 */
	abstract public function reset ();


	/**
	 * Returns identifier of a gauger
	 *
	 * @return string
	 */
	public function getName () {
		return $this->_name;
	}


	/**
	 * Gauges the subject's evaluation speed.
	 * If there is exception, it is going to be in details of fixed mark and thrown
	 * forth.
	 *
	 * @param Closure $subject Subject to gauge
	 * @param array $arguments Arguments for invocation
	 * @param string $marker Marker that denotes the subject
	 * @param array $details Details for that subject (optional)
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
	 * Set sequence filter.
	 * It will be working during the gauger's gathering of marks.
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
	 * Reset filters setup so that no more filters will be stored in the instance of gauger
	 *
	 * @return void
	 */
	public function resetFilters () {
		$this->_filters = array ();
	}


	/**
	 * Returns a report in Txt format
	 *
	 * @return string
	 */
	public function __toString () {
		$formatter = new TxtFormatter;

		return $formatter->gauger ($this);
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
	 * Returns a gauger by name. If it hasn't been created yet, it's going to be so.
	 * Furthermore, it's going to be stored in tha static scope of the class.
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