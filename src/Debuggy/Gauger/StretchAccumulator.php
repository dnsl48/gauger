<?php


namespace Debuggy\Gauger;


use Debuggy\Gauger;

use Debuggy\Gauger\Filter\Sequential as SequentialFilter;
use Debuggy\Gauger\Filter\Summary as SummaryFilter;

use Debuggy\Gauger\Mark\Sequential as SequentialMark;
use Debuggy\Gauger\Mark\Summary as SummaryMark;


/**
 * Gauges stretches between two points with the same markers.
 * All pointers are going to save until report.
 */
class StretchAccumulator extends Gauger {
	/** {@inheritdoc} */
	public function mark ($marker, $details = array ()) {
		$this->_makeMark ($marker, $details);
	}


	/** {@inheritdoc} */
	public function stamp ($stamp, $marker, $details = array ()) {
		$this->_makeMark ($marker, $details, $stamp);
	}


	/** {@inheritdoc} */
	public function reset () {
		$this->_marks_storage = array ();
		$this->_marks_order = array ();
		$this->_marks_details = array ();
	}


	/** {@inheritdoc} */
	public function getMarks (array $sequentialFilters = array (), array $summaryFilters = array ()) {
		$sequence = array ();
		$summary = array ();
		$tmpDetails = array ();

		foreach ($this->_marks_order as $markerOrderIdx => $marker) {
			if (!isset ($summary[$marker])) {
				$summary[$marker] = new SummaryMark;
				$summary[$marker]->marker = $marker;
				$summary[$marker]->duration = 0;
				$summary[$marker]->count = 1;
			} else {
				++$summary[$marker]->count;
			}

			if ($summary[$marker]->count % 2) {
				$tmpDetails[$marker] = isset ($this->_marks_details[$markerOrderIdx]) ? $this->_marks_details[$markerOrderIdx] : null;
				continue;
			}

			$markerStamps = $this->_marks_storage[$marker][floor ($summary[$marker]->count / 2)];

			$mark = new SequentialMark;
			$mark->marker = $marker;
			$mark->duration = $markerStamps[1] - $markerStamps[0];
			$mark->number = floor (($markerOrderIdx + 1) / 2);

			if ($tmpDetails[$marker] || $this->_marks_details[$markerOrderIdx])
				$mark->extra = array ('former' => $tmpDetails[$marker], 'latter' => $this->_marks_details[$markerOrderIdx]);

			$sequence[] = $mark;

			unset ($tmpDetails[$marker]);

			$summary[$marker]->duration += $markerStamps[1] - $markerStamps[0];
		}

		foreach ($summary as $marker => $mark) {
			$mark->count = floor ($mark->count / 2);

			if (!$mark->count)
				unset ($summary[$marker]);
		}

		if ($sequentialFilters) {
			for ($s = 0, $sc = count ($sequence); $s < $sc; ++$s) {
				for ($f = 0, $fc = count ($sequentialFilters); $f < $fc; ++$f) {
					if (!$sequentialFilters[$f]->checkSequential ($sequence[$s])) {
						unset ($sequence[$s]);
					}
				}
			}
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

		return array_merge ($sequence, $summary);
	}


	/** {@inheritdoc} */
	protected function getGaugeMarkers ($originalMarker) {
		return array (
			'before' => $originalMarker,
			'after' => $originalMarker,
			'exception' => $originalMarker
		);
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

		if (!isset ($this->_marks_storage[$marker]))
			$this->_marks_storage[$marker] = array (array ());

		$this->_marks_order[] = $marker;
		$this->_marks_details[] = $details ? $details : null;

		$idx = count ($this->_marks_storage[$marker]) - 1;

		if (count ($this->_marks_storage[$marker][$idx]) === 1) {
			$this->_marks_storage[$marker][$idx][] = $stamp;

			if ($filters = $this->getFilters ()) {
				$mark = new SequentialMark;
				$mark->marker = $marker;
				$mark->duration = $stamp - $this->_marks_storage[$marker][$idx][0];
				$mark->extra = $details ? $details : null;
				$mark->number = $idx+1;

				for ($i=0, $c=count($filters); $i < $c; ++$i) {
					if (!$filters[$i]->checkSequential ($mark)) {
						array_pop ($this->_marks_order);
						array_pop ($this->_marks_details);
						unset ($this->_marks_storage[$marker][$idx]);
						break;
					}
				}
			}
		} else
			// if the stamp is the first in the couple, it should be without method's overhead
			$this->_marks_storage[$marker][$idx+1][] = &$stamp;

		// bottom stamp without method's overhead (should be first in the couple of stamps)
		$stamp = $stamp ? $stamp : microtime (true);
	}


	/**
	 * Storage of stamps and marks
	 *
	 * @var array
	 */
	private $_marks_storage = array ();


	/**
	 * Order of marks
	 *
	 * @var array
	 */
	private $_marks_order = array ();


	/**
	 * Details of markers
	 *
	 * @var array
	 */
	private $_marks_details = array ();
}