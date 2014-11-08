<?php


namespace Debuggy\Gauger\Mark;


use Debuggy\Gauger\Mark;


/**
 * Summarizes an info about marker in a sequence of them
 */
class Summary extends Mark {
	/**
	 * Count of marks that have been summarized by this
	 *
	 * @var int
	 */
	public $count;
}