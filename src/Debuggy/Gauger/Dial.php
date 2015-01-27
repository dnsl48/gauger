<?php


namespace Debuggy\Gauger;



/**
 * Contains a chain of collectors.
 * If one collector cannot store a value because of filters, all chain is idling or erasing.
 */
class Dial {
	public function __construct (Indicator $indicator, Filter $filter = null) {
		$this->_indicator = $indicator;
		$this->_filter = $filter;
	}


	/**
	 * Makes new stamp into the collection.
	 * If filter restricts to add the stamp to the collection, false will be returned.
	 *
	 * @param string $id Stamp identifier
	 * @param mixed $extra Extra info provided by users
	 *
	 * @return bool
	 */
	public function stamp ($id, $extra = null) {
		$stamp = new Stamp;

		$stamp->id = $id;
		$stamp->value = $this->getIndicator ()->gauge ();
		$stamp->extra = $extra;

		if (($filter = $this->getFilter ()) && !$filter->checkStamp ($stamp))
			return false;

		$this->_collection[] = $stamp;

		return true;
	}


	/**
	 * Makes an empty gauge
	 *
	 * @return void
	 */
	public function idle () {
		$this->getIndicator ()->idle ();
	}


	/**
	 * Deletes a last stamp with the $id from the collection
	 *
	 * @param string $id Stamp identifier
	 *
	 * @return void
	 */
	public function erase ($id) {
		for ($i = count ($this->_collection) - 1; $i >= 0; --$i) {
			if ($this->_collection[$i]->id === $id) {
				array_splice ($this->_collection, $i, 1);
				break;
			}
		}
	}


	/**
	 * Returns a collection of harvested stamps
	 *
	 * @return Stamp[]
	 */
	public function getCollection () {
		return $this->_collection;
	}


	/**
	 * Returns filter
	 *
	 * @return Filter
	 */
	public function getFilter () {
		return $this->_filter;
	}


	/**
	 * Returns indicator
	 *
	 * @return Indicator
	 */
	public function getIndicator () {
		return $this->_indicator;
	}



	/**
	 * Collection of stamps
	 *
	 * @var Stamp[]
	 */
	private $_collection = array ();


	/**
	 * Filter for stamps
	 *
	 * @var Filter
	 */
	private $_filter;


	/**
	 * Indicator instance
	 *
	 * @var Indicator
	 */
	private $_indicator;
}