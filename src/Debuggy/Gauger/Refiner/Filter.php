<?php


namespace Debuggy\Gauger\Refiner;


use Debuggy\Gauger\Filter as _Filter;
use Debuggy\Gauger\Gauge;
use Debuggy\Gauger\Refiner;



/**
 * Refines a collection of stamps by a filter.
 * Each Dial has its own thread of stamps, whereas a filter can be applied to one thread only at once.
 * This refiner keeps relations between different threads so if a stamp is thrown away from some thread, the same indices
 * will be swept away in the others as well.
 */
class Filter extends Refiner {
	/**
	 * Initializes the instance with source refiner, the filter and an index
	 * of stamps collection to filter
	 *
	 * @param Refiner $refiner Source refiner
	 * @param _Filter $filter Filter instance
	 * @param int $thread Thread of stamps that should be filtered
	 */
	public function __construct (Refiner $refiner, _Filter $filter, $thread = 0) {
		parent::__construct ($refiner);

		$this->_filter = $filter;
		$this->_thread = $thread;
	}


	/** {@inheritdoc} */
	public function getStamps () {
		$stamps = parent::getStamps ();

		$tmp = $this->_filter->checkBunch ($stamps[$this->_thread]);

		if (count ($tmp) === count ($stamps[$this->_thread]))
			return $stamps;

		$result = array ();
		for ($i = 0, $c = count ($stamps); $i < $c; ++$i)
			$result[] = array ();

		for ($i = 0, $c = count ($tmp); $i < $c; ++$i) {
			for ($j = 0, $m = count ($stamps[$this->_thread]); $j < $m; ++$j) {
				if ($tmp[$i] === $stamps[$this->_thread][$j]) {
					for ($k = 0, $t = count ($stamps); $k < $t; ++$k)
						$result[$k][$i] = $stamps[$k][$j];
					break;
				}
			}
		}

		return $result;
	}



	/**
	 * Filter instance
	 *
	 * @var _Filter
	 */
	private $_filter;


	/**
	 * Index of thread that is used for choosing a collection of stamps
	 *
	 * @var int
	 */
	private $_thread;
}