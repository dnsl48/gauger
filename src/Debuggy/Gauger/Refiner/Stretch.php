<?php


namespace Debuggy\Gauger\Refiner;


use Debuggy\Gauger\Indicator;
use Debuggy\Gauger\Refiner;
use Debuggy\Gauger\Stamp;



/**
 * Groups stamps by pairs and calculates differences between them so that the result
 * contains list of stretches.
 */
class Stretch extends Refiner {
	/** {@inheritdoc} */
	public function getStamps () {
		$indicators = $this->getIndicators ();
		$stamps = parent::getStamps ();

		$result = array ();

		for ($i = 0, $c = count ($stamps); $i < $c; ++$i) {
			$result[$i] = $this->_flattenTree (
				$this->_calculateTree (
					$this->_justifyTree (
						$this->_buildTree ($stamps[$i])
					),
					$indicators[$i]
				)
			);
		}

		return $result;
	}


	/**
	 * Flattens the tree into a list of stamps
	 *
	 * @param array $tree Tree to be flattened
	 *
	 * @return Stamp[]
	 */
	private function _flattenTree (array $tree) {
		$result = array ();

		foreach ($tree as $key => $ptr) {
			for ($i = 0, $c = count ($ptr); $i < $c; ++$i) {
				if (isset ($ptr[$i]['val']))
					$result[] = $ptr[$i]['val'];

				if (isset ($ptr[$i]['<-']) || isset ($ptr[$i]['->'])) {
					foreach ($ptr[$i] as $pkey => $pval) {
						if ($pkey !== '<-' && $pkey !== '->')
							continue;

						$result = array_merge ($result, $this->_flattenTree ($pval));
					}
				}
			}
		}

		return $result;
	}


	/**
	 * Calculate tree values by subtracting values of the nodes
	 *
	 * @param array $tree Tree to be calculated
	 *
	 * @return array
	 */
	private function _calculateTree (array $tree, Indicator $indicator) {
		foreach ($tree as $key => $ptr) {
			for ($i = 0, $c = count ($ptr); $i < $c; ++$i) {
				if (isset ($ptr[$i]['fst']) && isset ($ptr[$i]['snd'])) {
					$stamp = new Stamp;
					$stamp->id = $ptr[$i]['fst']->id;
					$stamp->value = $indicator->sub ($ptr[$i]['snd']->value, $ptr[$i]['fst']->value);

					$extra = array ();

					if (isset ($ptr[$i]['fst']->extra))
						$extra['fst'] = $ptr[$i]['fst']->extra;

					if (isset ($ptr[$i]['snd']->extra))
						$extra['snd'] = $ptr[$i]['snd']->extra;

					if ($extra)
						$stamp = $extra;

					$tree[$key][$i]['val'] = $stamp;
				}

				if (isset ($ptr[$i]['<-']))
					$tree[$key][$i]['<-'] = $this->_calculateTree ($tree[$key][$i]['<-'], $indicator);

				if (isset ($ptr[$i]['->'])) {
					$tree[$key][$i]['->'] = $this->_calculateTree ($tree[$key][$i]['->'], $indicator);

					if (isset ($tree[$key][$i]['val']))
						$this->_subtractTree ($tree[$key][$i]['val'], $tree[$key][$i]['->'], $indicator);
				}
			}
		}

		return $tree;
	}


	/**
	 * Subtracts all values of the tree from the value
	 *
	 * @param Stamp $stamp Stamp with value for subtraction
	 * @param array $tree Tree to be subtracted
	 *
	 * @return void
	 */
	private function _subtractTree (Stamp $stamp, array $tree, Indicator $indicator) {
		foreach ($tree as $key => $ptr) {
			for ($i = 0, $c = count ($ptr); $i < $c; ++$i) {
				if (isset ($ptr[$i]['val']))
					$stamp->value = $indicator->sub ($stamp->value, $ptr[$i]['val']->value);

				if (isset ($ptr[$i]['->']))
					$this->_subtractTree ($stamp, $ptr[$i]['->'], $indicator);
			}
		}
	}


	/**
	 * Looks for empty nodes in a tree and figuring out their values relying on a parent node's value
	 *
	 * @param array $node Tree to be justified
	 * @param array $parent Parent node
	 *
	 * @return array
	 */
	private function _justifyTree (array $node, array $parent = null) {
		foreach ($node as $key => $ptr) {
			for ($i = 0, $c = count ($ptr); $i < $c; ++$i) {
				if (!isset ($ptr[$i]['snd']) && isset ($parent['snd']))
					$node[$key][$i]['snd'] = $parent['snd'];

				if (isset ($ptr[$i]['<-']))
					$node[$key][$i]['<-'] = $this->_justifyTree ($ptr[$i]['<-'], $ptr[$i]);

				if (isset ($ptr[$i]['->']))
					$node[$key][$i]['->'] = $this->_justifyTree ($ptr[$i]['->'], $ptr[$i]);
			}
		}

		return $node;
	}


	/**
	 * Makes a tree from stamps. Decisions about relations are being made
	 * by separators of stamp ids. Separators are '->' and '<-'.
	 *
	 * @param Stamp[] $stamps List of stamps
	 *
	 * @return array
	 */
	private function _buildTree ($stamps) {
		$tree = array ();

		for ($i = 0, $ic = count ($stamps); $i < $ic; ++$i) {
			$ids = $this->_parseId ($stamps[$i]->id);

			$ptr =& $tree;
			for ($j = 0, $jc = count ($ids); $j < $jc; ++$j) {
				if ($j === $jc - 1) {
					if (!isset ($ptr[$ids[$j]]))
						$ptr[$ids[$j]] = array (array ('fst' => $stamps[$i], 'snd' => null));

					else if (!isset ($ptr[$ids[$j]][count ($ptr[$ids[$j]]) - 1]['snd']))
						$ptr[$ids[$j]][count ($ptr[$ids[$j]]) - 1]['snd'] = $stamps[$i];

					else
						$ptr[$ids[$j]][] = array ('fst' => $stamps[$i], 'snd' => null);

					break;
				}

				if ($ids[$j] === '<-' || $ids[$j] === '->') {
					if (!isset ($ptr[$ids[$j]]))
						$ptr[$ids[$j]] = array ();

					$ptr =& $ptr[$ids[$j]];
					continue;
				}

				if (!isset ($ptr[$ids[$j]]) || isset ($ptr[$ids[$j]][count ($ptr[$ids[$j]]) - 1]['snd'])) {
					$ids[$j+1] = $ids[$j].$ids[$j+1];
					continue;
				}

				$ptr =& $ptr[$ids[$j]][count ($ptr[$ids[$j]]) - 1];
			}
			unset ($ptr);
		}

		return $tree;
	}


	/**
	 * Parse identifier of a stamp so that its relations are separators.
	 * Returns list of separated IDs with their separators.
	 * There are two possible separators: '->' and '<-'.
	 *
	 * @param string $stampId Stamp identifier
	 * @param int $offset Offset for parsing
	 *
	 * @return string[]
	 */
	private function _parseId ($stampId, $offset = 0) {
		if (($pos = strpos ($stampId, '-', $offset)) !== false) {
			if (substr ($stampId, $pos - 1, 1) === '<')
				return array_merge (array (substr ($stampId, 0, $pos - 1), '<-'), $this->_parseId (substr ($stampId, $pos + 1)));

			else if (substr ($stampId, $pos + 1, 1) === '>')
				return array_merge (array (substr ($stampId, 0, $pos), '->'), $this->_parseId (substr ($stampId, $pos + 2)));

			else
				return $this->_parseId ($stampId, $pos + 1);
		} else
			return array ($stampId);
	}
}