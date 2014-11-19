<?php


namespace Debuggy\Gauger\Reporter;


use Debuggy\Gauger;

use Debuggy\Gauger\Reporter;

use Debuggy\Gauger\Mark;
use Debuggy\Gauger\Mark\Sequential as SequentialMark;
use Debuggy\Gauger\Mark\Summary as SummaryMark;


/**
 * Transforms marks to php-array
 */
class PhpArray extends Reporter {
	/** {@inheritdoc} */
	public function gaugers (array $gaugers) {
		$result = array ();

		foreach ($gaugers as $gauger)
			$result += $this->gauger ($gauger);

		return $result;
	}

	/** {@inheritdoc} */
	public function gauger (Gauger $gauger) {
		return array ($gauger->getName () => $this->marks ($gauger->getMarks ()));
	}


	/** {@inheritdoc} */
	public function marks (array $marks) {
		$result = array ();

		foreach ($marks as $idx => $mark)
			$result[$idx] = $this->mark ($mark);

		return $result;
	}


	/** {@inheritdoc} */
	public function mark (Mark $mark) {
		$result = array (
			'marker' => $mark->marker,
			'gauge' => $mark->gauge,
			'extra' => $this->_transformExtraToArrayOfScalars ($mark->extra)
		);

		if ($mark instanceof SequentialMark)
			$this->_sequentialMark ($mark, $result);

		else if ($mark instanceof SummaryMark)
			$this->_summaryMark ($mark, $result);

		return $result;
	}


	/**
	 * Extracts SequentialMark parameters into the array
	 *
	 * @param SequentialMark $mark Mark for extraction
	 * @param array &$result Result array
	 *
	 * @return void
	 */
	private function _sequentialMark (SequentialMark $mark, array &$result) {
		$result['number'] = $mark->number;
	}


	/**
	 * Extracts SummaryMark parameters into the array
	 *
	 * @param SummaryMark $mark Mark for extraction
	 * @param array &$result Result array
	 *
	 * @return void
	 */
	private function _summaryMark (SummaryMark $mark, array &$result) {
		$result['count'] = $mark->count;
	}


	/**
	 * Transforms extra data to array of scalars
	 *
	 * @param array $extra Extra data
	 *
	 * @return array
	 */
	private function _transformExtraToArrayOfScalars (array $extra) {
		$result = array ();

		foreach ($extra as $key => $value) {
			if (is_scalar ($value))
				$result[$key] = $value;
			else if (is_array ($value))
				$result[$key] = $this->_transformExtraToArrayOfScalars ($value);
			else
				$result[$key] = (string) $value;
		}

		if (!$result)
			$result = null;

		return $result;
	}
}