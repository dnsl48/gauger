<?php


namespace Debuggy\Gauger;



/**
 * Sample of the Gauger components usage.
 * It also can be used as a pattern for some often situations.
 */
interface Sample {
	/**
	 * Returns an instance of a gauge
	 *
	 * @return Gauge
	 */
	public function getGauge ();


	/**
	 * Returns the recount as an array
	 *
	 * @return array
	 */
	public function toArray ();


	/**
	 * Makes the recount through the Txt reporter
	 *
	 * @return string
	 */
	public function toString ();
}