<?php


namespace Debuggy\Gauger;


/**
 * Mark contains gathered info about marker
 */
class Mark {
	/**
	 * Name of the marker
	 *
	 * @var string
	 */
	public $marker;

	/**
	 * Duration of the marker's evaluation
	 *
	 * @var float
	 */
	public $duration;

	/**
	 * Extra information of the mark (mixed array)
	 *
	 * @var array
	 */
	public $extra = array ();
}