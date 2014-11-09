<?php


namespace Debuggy\Gauger;


use Debuggy\Gauger;

use Debuggy\Gauger\Filter\Sequential as SequentialFilter;
use Debuggy\Gauger\Filter\Summary as SummaryFilter;

use Debuggy\Gauger\Mark\Sequential as SequentialMark;
use Debuggy\Gauger\Mark\Summary as SummaryMark;


/**
 * Gauges stretches between two points with the same markers.
 * For each marker will be kept only summary information.
 */
abstract class StretchCalculator extends Gauger {
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
			$summary[$marker]->gauge = $stamps[0];
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
	 * Makes mark's data and keep it in the object
	 *
	 * @param string $marker Marker name
	 * @param array $details Extra data for that marker (optional)
	 * @param float $gauge Gauge
	 *
	 * @return void
	 */
	private function _makeMark ($marker, $details = array (), $gauge = null) {
		// top stamp that does not have any method's overhead
		$gauge = $gauge ? $gauge : $this->getGauge ($details);

		if (!isset ($this->_marks_storage[$marker])) {
			$this->_marks_storage[$marker] = array (0);
			$this->_marks_count[$marker] = 1;
			$this->_marks_details[$marker] = $details ? array ($details) : array (null);
		} else {
			++$this->_marks_count[$marker];
			$this->_marks_details[$marker][] = $details ? $details : null;
		}

		if (isset ($this->_marks_storage[$marker][1])) {
			$delta = $gauge - $this->_marks_storage[$marker][1];

			$filtersResult = true;

			if ($filters = $this->getFilters ()) {
				$mark = new SequentialMark;
				$mark->marker = $marker;
				$mark->gauge = $delta;
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
			$this->_marks_storage[$marker][1] = &$gauge;

		// bottom stamp that does not have any method's overhead
		$gauge = $gauge ? $gauge : $this->getGauge ($details);
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
	 * Storage for stamps and marks
	 *
	 * @var array
	 */
	private $_marks_storage = array ();


	/**
	 * Conter of marks
	 *
	 * @var array
	 */
	private $_marks_count = array ();


	/**
	 * Markers extra info
	 *
	 * @var array
	 */
	private $_marks_details = array ();
}