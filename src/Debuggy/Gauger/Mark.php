<?php


namespace Debuggy\Gauger;


/**
 * Business Object that contains gathered info about marker
 */
class Mark {
	/**
	 * Marker name
	 *
	 * @var string
	 */
	public $marker;

	/**
	 * Marker's evaluation gauge
	 *
	 * @var float
	 */
	public $gauge;

	/**
	 * Extra information of the mark (mixed array)
	 *
	 * @var array
	 */
	public $extra = array ();
}