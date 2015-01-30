<?php


namespace Debuggy\Gauger\Formatter;


use Debuggy\Gauger\Formatter;



/**
 * Provides the ability to hide indicator values from a report
 */
class Stash extends Formatter {
	/** {@inheritdoc} */
	public function isVisible ($value) {
		return false;
	}
}