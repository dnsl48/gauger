<?php


namespace Debuggy\Gauger;


use Debuggy\Gauger;

use Debuggy\Gauger\Filter\Sequential as SequentialFilter;
use Debuggy\Gauger\Filter\Summary as SummaryFilter;

use Debuggy\Gauger\Mark\Sequential as SequentialMark;
use Debuggy\Gauger\Mark\Summary as SummaryMark;


/**
 * Gauges stretches between two points with the same markers.
 * For each pointer will be saved info about total amount of evaluation time and count of invocations.
 */
class StretchCalculator extends Gauger {
	/**
	 * {@inheritdoc}
	 */
	public function mark ($marker, $details = array ()) {
		$this->_makeMark ($marker, $details);
	}


	/**
	 * {@inheritdoc}
	 */
	public function stamp ($stamp, $marker, $details = array ()) {
		$this->_makeMark ($marker, $details, $stamp);
	}


	/** {@inheritdoc} */
	public function getMarks (array $sequentialFilters = array (), array $summaryFilters = array ()) {
		$summary = array ();

		foreach ($this->_marks_storage as $marker => $stamps) {
			if (!$this->_marks_count[$marker])
				continue;

			$summary[$marker] = new SummaryMark;
			$summary[$marker]->marker = $marker;
			$summary[$marker]->duration = $stamps[0];
			$summary[$marker]->count = $this->_marks_count[$marker] / 2;
			$summary[$marker]->extra = array_filter (
				array_combine (
					array_map (
						function ($key) {return $key + 1;},
						array_keys ($this->_marks_details[$marker])
					),
					array_values ($this->_marks_details[$marker])
				),
				function ($val) {return !is_null ($val);}
			);
		}

		if ($summaryFilters) {
			foreach ($summary as $s => $mark) {
				for ($f = 0, $fc = count ($summaryFilters); $f < $fc; ++$f) {
					if (!$summaryFilters[$f]->checkSummary ($mark)) {
						unset ($summary[$s]);
					}
				}
			}
		}

		return $summary;
	}


	/** {@inheritdoc} */
	public function reset () {
		$this->_marks_storage = array ();
		$this->_marks_count = array ();
		$this->_marks_details = array ();
	}


	/**
	 * Makes mark's data and store that in internal arrays
	 *
	 * @param string $marker Marker that denotes the stamp
	 * @param array $details Note for that stamp (optional)
	 * @param float $stamp Microtime stamp
	 *
	 * @return void
	 */
	private function _makeMark ($marker, $details = array (), $stamp = null) {
		// top stamp without method's overhead (should be second in the couple of stamps)
		$_stamp = microtime (true);
		$stamp = $stamp ? $stamp : $_stamp;

		if (!isset ($this->_marks_storage[$marker])) {
			$this->_marks_storage[$marker] = array (0);
			$this->_marks_count[$marker] = 1;
			$this->_marks_details[$marker] = $details ? array ($details) : array (null);
		} else {
			++$this->_marks_count[$marker];
			$this->_marks_details[$marker][] = $details ? $details : null;
		}

		if (isset ($this->_marks_storage[$marker][1])) {
			$delta = $stamp - $this->_marks_storage[$marker][1];

			$filtersResult = true;

			if ($filters = $this->getFilters ()) {
				$mark = new SequentialMark;
				$mark->marker = $marker;
				$mark->duration = $delta;
				$mark->extra = $details ? $details : null;
				$mark->number = $this->_marks_count[$marker];

				for ($i=0, $c=count($filters); $i < $c; ++$i) {
					if (!$filters[$i]->checkSequential ($mark)) {
						$filtersResult = false;
						break;
					}
				}
			}

			if ($filtersResult)
				$this->_marks_storage[$marker][0] += $delta;
			else {
				$this->_marks_count[$marker] -= 2;
				array_pop ($this->_marks_details[$marker]);
				array_pop ($this->_marks_details[$marker]);
			}

			unset ($this->_marks_storage[$marker][1]);
		} else
			$this->_marks_storage[$marker][1] = &$stamp;

		// bottom stamp without method's overhead (should be first in the couple of stamps)
		$stamp = $stamp ? $stamp : microtime(true);
	}


	/**
	 * {@inheritdoc}
	 */
	protected function getGaugeMarkers ($originalMarker) {
		return array (
			'before' => $originalMarker,
			'after' => $originalMarker,
			'exception' => $originalMarker
		);
	}


	/**
	 * Storage of stamps and marks
	 *
	 * @var array
	 */
	private $_marks_storage = array ();


	/**
	 * Conter of marks' invocations
	 *
	 * @var array
	 */
	private $_marks_count = array ();


	/**
	 * Details of markers
	 *
	 * @var array
	 */
	private $_marks_details = array ();
}