<?php


namespace Debuggy\Gauger;


use Debuggy\Gauger;

use Debuggy\Gauger\Filter\Sequential as SequentialFilter;
use Debuggy\Gauger\Filter\Summary as SummaryFilter;

use Debuggy\Gauger\Mark\Sequential as SequentialMark;
use Debuggy\Gauger\Mark\Summary as SummaryMark;


/**
 * Gauges stretches between two points with the same markers.
 * All marks are going to be kept until a report generation.
 */
abstract class StretchAccumulator extends Gauger {
	/** {@inheritdoc} */
	public function mark ($marker, $extra = array ()) {
		$this->_makeMark ($marker, $extra);
	}


	/** {@inheritdoc} */
	public function stamp ($stamp, $marker, $extra = array ()) {
		$this->_makeMark ($marker, $extra, $stamp);
	}


	/** {@inheritdoc} */
	public function reset () {
		$this->_marks_storage = array ();
		$this->_marks_order = array ();
		$this->_marks_extra = array ();
	}


	/** {@inheritdoc} */
	public function getMarks (array $sequentialFilters = array (), array $summaryFilters = array ()) {
		$sequence = array ();
		$summary = array ();
		$tmpExtra = array ();

		foreach ($this->_marks_order as $markerOrderIdx => $marker) {
			if (!isset ($summary[$marker])) {
				$summary[$marker] = new SummaryMark;
				$summary[$marker]->marker = $marker;
				$summary[$marker]->gauge = 0;
				$summary[$marker]->count = 1;
			} else {
				++$summary[$marker]->count;
			}

			if ($summary[$marker]->count % 2) {
				$tmpExtra[$marker] = isset ($this->_marks_extra[$markerOrderIdx]) ? $this->_marks_extra[$markerOrderIdx] : null;
				continue;
			}

			$markerStamps = $this->_marks_storage[$marker][floor ($summary[$marker]->count / 2)];

			$mark = new SequentialMark;
			$mark->marker = $marker;
			$mark->gauge = $markerStamps[1] - $markerStamps[0];
			$mark->number = floor (($markerOrderIdx + 1) / 2);

			if ($tmpExtra[$marker] || $this->_marks_extra[$markerOrderIdx])
				$mark->extra = array ('former' => $tmpExtra[$marker], 'latter' => $this->_marks_extra[$markerOrderIdx]);

			$sequence[] = $mark;

			unset ($tmpExtra[$marker]);

			$summary[$marker]->gauge += $markerStamps[1] - $markerStamps[0];
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
	 * Makes mark's data and keep it in the object
	 *
	 * @param string $marker Marker name
	 * @param array $extra Extra data for that marker (optional)
	 * @param float $gauge Gauge to stamp
	 *
	 * @return void
	 */
	private function _makeMark ($marker, $extra = array (), $gauge = null) {
		// top stamp that does not have any method's overhead
		$gauge = $gauge ? $gauge : $this->getGauge ($extra);

		if (!isset ($this->_marks_storage[$marker]))
			$this->_marks_storage[$marker] = array (array ());

		$this->_marks_order[] = $marker;
		$this->_marks_extra[] = $extra ? $extra : null;

		$idx = count ($this->_marks_storage[$marker]) - 1;

		if (count ($this->_marks_storage[$marker][$idx]) === 1) {
			$this->_marks_storage[$marker][$idx][] = $gauge;

			if ($filters = $this->getFilters ()) {
				$mark = new SequentialMark;
				$mark->marker = $marker;
				$mark->gauge = $gauge - $this->_marks_storage[$marker][$idx][0];
				$mark->extra = $extra ? $extra : null;
				$mark->number = $idx+1;

				for ($i=0, $c=count($filters); $i < $c; ++$i) {
					if (!$filters[$i]->checkSequential ($mark)) {
						array_pop ($this->_marks_order);
						array_pop ($this->_marks_extra);
						unset ($this->_marks_storage[$marker][$idx]);
						break;
					}
				}
			}
		} else
			// if the stamp is the first in the couple, it should not have include method's overhead
			$this->_marks_storage[$marker][$idx+1][] = &$gauge;

		// bottom stamp that does not have any method's overhead
		$gauge = $gauge ? $gauge : $this->getGauge ($extra);
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
	 * Extra info of markers
	 *
	 * @var array
	 */
	private $_marks_extra = array ();
}