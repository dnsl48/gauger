<?php


namespace Debuggy\Gauger\Reporter;


use Debuggy\Gauger\Refiner;
use Debuggy\Gauger\Reporter;



/**
 * Calculates summary and average values for each indicator
 */
class Summary implements Reporter {
	/** {@inheritdoc} */
	public function recount (Refiner $refiner) {
		$indicators = $refiner->getIndicators ();
		$stamps = $refiner->getStamps ();

		$summary = array ();

		for ($t = 0, $tc = count ($stamps); $t < $tc; ++$t) {
			$result = array ();

			for ($i = 0, $c = count ($stamps[$t]); $i < $c; ++$i) {
				if (!isset ($result[$stamps[$t][$i]->id])) {
					$result[$stamps[$t][$i]->id] = array (
						'cnt' => 0,
						'sum' => null,
						'avg' => null,
						'vls' => array ()
					);
				}

				++$result[$stamps[$t][$i]->id]['cnt'];
				$result[$stamps[$t][$i]->id]['vls'][] = $stamps[$t][$i]->value;
			}

			foreach ($result as $stampId => $stampData) {
				$result[$stampId]['sum'] = array_reduce (array_slice ($result[$stampId]['vls'], 1), function ($sum, $value) use ($t, $indicators) {
					return $indicators[$t]->sum ($sum, $value);
				}, $result[$stampId]['vls'][0]);

				$result[$stampId]['avg'] = $indicators[$t]->avg ($result[$stampId]['vls']);

				unset ($result[$stampId]['vls']);
			}

			$summary[] = $result;
		}

		$stampsCount = 0;

		$result = array ();

		for ($t = 0, $c = count ($summary); $t < $c; ++$t) {
			$formatter = $indicators[$t]->getFormatter ();
			foreach ($summary[$t] as $stampId => $data) {
				if (!$formatter->isVisible ($data['sum']) && !$formatter->isVisible ($data['avg']))
					continue;

				$row = array ('cnt' => $data['cnt']);

				if ($formatter->isVisible ($data['sum']))
					$row['sum'] = $formatter->format ($data['sum']);

				if ($formatter->isVisible ($data['avg']))
					$row['avg'] = $formatter->format ($data['avg']);

				$iname = $indicators[$t]->getName ();
				$idx = -1;

				for ($i = count ($result) - 1; $i >= 0; --$i) {
					if ($result[$i][0] === $stampId) {
						$idx = $i;
						break;
					}
				}

				if ($idx === -1)
					$result[] = array ($stampId, array ($iname => $row));

				else if (!isset ($result[$idx][1][$iname]))
					$result[$idx][1][$iname] = $row;

				else if (isset ($result[$idx][1][$iname]['sum']))
					$result[$idx][1][$iname] = array ($result[$idx][1][$iname], $row);

				else
					$result[$idx][1][$iname][] = $row;
			}
		}

		return $result;
	}
}