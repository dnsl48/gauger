<?php


namespace Debuggy\Gauger\Refiner;


use Debuggy\Gauger\Refiner;
use Debuggy\Gauger\Stamp;



/**
 * Produces a new set of stamps with only total values for each stamp->id
 */
class Total extends Refiner {
	/** {@inheritdoc} */
	public function getStamps () {
		$indicators = $this->getIndicators ();
		$stamps = parent::getStamps ();

		$result = array ();
		for ($t = 0, $tc = count ($stamps); $t < $tc; ++$t) {
			$result[$t] = array ();
			$map = array ();

			for ($i = 0, $c = count ($stamps[$t]); $i < $c; ++$i) {
				if (!isset ($map[$stamps[$t][$i]->id])) {
					$stamp = new Stamp;

					$stamp->id = $stamps[$t][$i]->id;
					$stamp->value = $stamps[$t][$i]->value;

					$map[$stamp->id] = $stamp;
					$result[$t][] = $stamp;

				} else {
					$map[$stamps[$t][$i]->id]->value = $indicators[$t]->sum (
						$map[$stamps[$t][$i]->id]->value,
						$stamps[$t][$i]->value
					);
				}
			}
		}

		return $result;
	}
}