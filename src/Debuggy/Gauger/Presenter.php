<?php


namespace Debuggy\Gauger;



/**
 * Presenter of reported data
 */
interface Presenter {
	/**
	 * Converts the data into an appropriate representation and returns it
	 *
	 * @param array $data Data to be represented
	 *
	 * @return mixed
	 */
	public function represent (array $data);
}