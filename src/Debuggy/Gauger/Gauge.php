<?php


namespace Debuggy\Gauger;


use Closure;



/**
 * Contains number of dials and makes access to them
 */
class Gauge {
	/**
	 * Makes stamps for each dial.
	 * If a dial returns false, all previous stamps will be erased and next ones won't be made.
	 *
	 * @param string $id Stamp identifier
	 * @param mixed $extra Extra info provided by a user
	 *
	 * @return void
	 */
	public function stamp ($id, $extra = null) {
		for ($i = 0, $c = count ($this->_dials); $i < $c; ++$i) {
			if (!$this->_dials[$i]->stamp ($id, $extra)) {
				for ($j = $i - 1; $j >= 0; --$j)
					$this->_dials[$j]->erase ($id);

				for ($j = $i + 1; $j < $c; ++$j)
					$this->_dials[$j]->idle ($extra);

				break;
			}
		}
	}


	/**
	 * Erases last stamp from each dial
	 *
	 * @param string $id Stamp identifier
	 *
	 * @return void
	 */
	public function erase ($id) {
		for ($i = 0, $c = count ($this->_dials); $i < $c; ++$i)
			$this->_dials[$i]->erase ($id);
	}


	/**
	 * Add a dial to the gauge
	 *
	 * @param Dial $dial Dial to be appended to the gauge
	 *
	 * @return void
	 */
	public function addDial (Dial $dial) {
		$this->_dials[] = $dial;
	}


	/**
	 * Returns the dials of the gauge
	 *
	 * @return Dial[]
	 */
	public function getDials () {
		return $this->_dials;
	}



	/**
	 * List of dials
	 *
	 * @var Dial[]
	 */
	private $_dials = array ();
}