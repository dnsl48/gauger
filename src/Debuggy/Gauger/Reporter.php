<?php


namespace Debuggy\Gauger;



/**
 * Formats gathered info into a report
 */
interface Reporter {
	/**
	 * Recount details of the gauge
	 *
	 * @return array
	 */
	public function recount (Refiner $refiner);
}