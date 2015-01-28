<?php


namespace Debuggy\Gauger\Sample;


use Debuggy\Gauger\Dial;
use Debuggy\Gauger\Filter;
use Debuggy\Gauger\Gauge;
use Debuggy\Gauger\Indicator;
use Debuggy\Gauger\Refiner;
use Debuggy\Gauger\Reporter;
use Debuggy\Gauger\Sample;



/**
 * Timer sample contains only microtime measuring and two parts of report: stretches and their summary
 */
class Timer extends Sample {
	/**
	 * Initializes the object with values of minimal and maximal time intervals
	 * for a stretch or its total sum to pass into the report.
	 *
	 * @param float $min Minimal time interval
	 * @param float $max Maximal time interval
	 * @param float $totalMin Minimal time interval for a stamp's total
	 * @param float $totalMax Maximal time interval for a stamp's total
	 */
	public function __construct ($min = null, $max = null, $totalMin = null, $totalMax = null) {
		parent::__construct (new Gauge);

		$this->_min = $min;
		$this->_max = $max;
		$this->_totalMin = $totalMin;
		$this->_totalMax = $totalMax;

		$this->getGauge ()->addDial (new Dial (new Indicator\Microtime));
	}


	/** {@inheritdoc} */
	public function toArray () {
		$gauge = $this->getGauge ();

		$refiner = $this->_filter (new Refiner\Stretch (new Refiner\Root ($gauge)));
		$refiner = new Refiner\Cache ($refiner);

		$plainReporter = new Reporter\Plain;
		$summaryReporter = new Reporter\Summary;

		return array (
			'Stretch' => $plainReporter->recount ($refiner),
			'Summary' => $summaryReporter->recount ($refiner)
		);
	}


	/**
	 * Makes filters around the stretch refiner accordingly to the settings of min, max, totalMin and totalMax
	 *
	 * @param Refiner $refiner Refiner for filtration
	 *
	 * @return Refiner
	 */
	private function _filter (Refiner $refiner) {
		if (isset ($this->_min) || isset ($this->_max))
			$refiner = new Refiner\Filter ($refiner, new Filter\Between ($this->_min, $this->_max));


		if (isset ($this->_totalMin) || isset ($this->_totalMax)) {
			$refiner = new Refiner\Cache ($refiner);
			$filtered = new Refiner\Filter (new Refiner\Total ($refiner), new Filter\Between ($this->_totalMin, $this->_totalMax));

			$fstamps = $filtered->getStamps ();
			$ids = array ();

			/* Figures out a list of stamp->id regardingly to the result of the filtration of the Total refiner */
			for ($t = 0, $tc = count ($fstamps); $t < $tc; ++$t) {
				$ids[] = array ();

				for ($i = 0, $c = count ($fstamps[$t]); $i < $c; ++$i)
					$ids[$t][$fstamps[$t][$i]->id] = true;
			}

			/* Makes a new filter by stamp->id for each thread of stamps (each dial produces it's own thread of stamps) */
			for ($t = 0, $c = count ($ids); $t < $c; ++$t) {
				$refiner = new Refiner\Filter ($refiner, new Filter\Closure (null, function ($stamps) use ($ids, $t) {
					$result = array ();
					for ($i = 0, $c = count ($stamps); $i < $c; ++$i) {
						if (isset ($ids[$t][$stamps[$i]->id]))
							$result[] = $stamps[$i];
					}
					return $result;
				}), $t);
			}
		}

		return $refiner;
	}



	/**
	 * Min value for a stretch
	 *
	 * @var float
	 */
	private $_min;


	/**
	 * Max value for a stretch
	 *
	 * @var float
	 */
	private $_max;


	/**
	 * Min value for a total sum of stretches
	 *
	 * @var float
	 */
	private $_totalMin;


	/**
	 * Max value for a total sum of stretches
	 *
	 * @var float
	 */
	private $_totalMax;
}