<?php


namespace Debuggy\Gauger\Reporter;


use Debuggy\Gauger\Refiner;
use Debuggy\Gauger\Reporter;



/**
 * Reports data as is
 */
class Plain implements Reporter {
	/** {@inheritdoc} */
	public function recount (Refiner $refiner) {
		$indicators = $refiner->getIndicators ();
		$stamps = $refiner->getStamps ();

		$stampsCount = 0;
		$threadsCount = count ($stamps);

		for ($i = 0; $i < $threadsCount; ++$i)
			$stampsCount = max ($stampsCount, count ($stamps[$i]));

		$data = array ();

		for ($i = 0; $i < $stampsCount; ++$i) {
			$data[$i] = array ();
			for ($t = 0; $t < $threadsCount; ++$t) {
				if (!isset ($stamps[$t][$i]))
					continue;

				if (!$indicators[$t]->getFormatter ()->isVisible ($stamps[$t][$i]->value))
					continue;

				if (isset ($data[$i][0]) && $stamps[$t][$i]->id !== $data[$i][0])
					continue; // unsynchronized thread! TODO: Maybe exception would be better here

				if (!isset ($data[$i][0]))
					$data[$i][0] = $stamps[$t][$i]->id;

				if (!isset ($data[$i][1]))
					$data[$i][1] = array ();

				$iname = $indicators[$t]->getName ();

				$stampData = array ('val' => $indicators[$t]->getFormatter ()->format ($stamps[$t][$i]->value));

				if (!isset ($data[$i][1][$iname]))
					$data[$i][1][$iname] = $stampData;

				else if (isset ($data[$i][1][$iname]['val']))
					$data[$i][1][$iname] = array ($data[$i][1][$iname], $stampData);
				else
					$data[$i][1][$iname][] = $stampData;
			}
		}

		for ($i = 0, $c = count ($data); $i < $c; ++$i) {
			foreach ($data[$i][1] as $iname => $idata) {
				if (isset ($idata['val']) && count ($idata) === 1) {
					$data[$i][1][$iname] = $idata['val'];

				} else if (count ($idata)) {
					for ($j = 0, $jc = count ($idata); $j < $jc; ++$j) {
						if (isset ($idata[$j]['val']) && count ($idata[$j]) === 1)
							$data[$i][1][$iname][$j] = $idata[$j]['val'];
					}
				}
			}
		}


		return $data;
	}
}