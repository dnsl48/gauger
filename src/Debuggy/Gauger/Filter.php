<?php


namespace Debuggy\Gauger;


use Debuggy\Gauger\Stamp;



/**
 * Filter allows to apply some evaluations to stamps
 */
abstract class Filter {
	/**
	 * Evaluates a single stamp
	 *
	 * @param Stamp $stamp Stamp for evaluation
	 *
	 * @return bool
	 */
	abstract public function checkStamp (Stamp $stamp);


	/**
	 * Checks a bunch of stamps and returns appropriate ones only
	 *
	 * @param Stamp[] $stamps List of stamps for evaluation
	 *
	 * @return Stamp[]
	 */
	public function checkBunch (array $stamps) {
		$result = array ();

		for ($i = 0, $c = count($stamps); $i < $c; ++$i) {
			if ($this->checkStamp ($stamps[$i]))
				$result[] = $stamps[$i];
		}

		return $result;
	}
}