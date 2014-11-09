<?php


namespace Debuggy\Gauger\Mark;


use Debuggy\Gauger\Mark;


/**
 * Marker in a sequence of them
 */
class Sequential extends Mark {
	/**
	 * Number of the mark in order to other ones
	 *
	 * @var int
	 */
	public $number;
}