<?php


namespace Debuggy\Gauger;


/**
 * Stamp is a Business-Object that contains harvested data.
 */
class Stamp {
	/**
	 * Identifier of the stamp, given to it by a user
	 *
	 * @var string
	 */
	public $id;


	/**
	 * Raw value of an indicator's gauging
	 *
	 * @var mixed
	 */
	public $value;
}