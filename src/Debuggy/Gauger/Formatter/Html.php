<?php


namespace Debuggy\Gauger\Formatter;


use Debuggy\Gauger;

use Debuggy\Gauger\Formatter;

use Debuggy\Gauger\Mark;


/**
 * Implements logic of Info to Array transformation
 */
class Html extends Formatter {
	/**
	 * Whether do enclosing of the info into HTML context
	 *
	 * @param bool $enclose Enclose flag (true by default)
	 */
	public function __construct ($enclose = true, $outputWidth = 80, $border = '*', $filler = '.') {
		$this->_encloseFlag = $enclose;

		$this->_txtFormatter = new Txt ($outputWidth, $border, $filler);
	}


	/** {@inheritdoc} */
	public function gaugers (array $gaugers) {
		$result = htmlspecialchars ($this->_txtFormatter->gaugers ($gaugers));

		return $this->_encloseFlag ? $this->_enclose ($result) : $result;
	}


	/** {@inheritdoc} */
	public function gauger (Gauger $gauger) {
		$result = htmlspecialchars ($this->_txtFormatter->gauger ($gauger));

		return $this->_encloseFlag ? $this->_enclose ($result) : $result;
	}


	/** {@inheritdoc} */
	public function arrayOfMarks (array $marks) {
		$result = htmlspecialchars ($this->_txtFormatter->arrayOfMarks ($marks));

		return $this->_encloseFlag ? $this->_enclose ($result) : $result;
	}


	/** {@inheritdoc} */
	public function singleMark (Mark $mark) {
		$result = htmlspecialchars ($this->_txtFormatter->singleMark ($mark));

		return $this->_encloseFlag ? $this->_enclose ($result) : $result;
	}

	
	/**
	 * Returns the info, enclosed into html tags
	 *
	 * @param string $info Info to enclose
	 *
	 * @return string
	 */
	private function _enclose ($info) {
		return '<!DOCTYPE html><html><head><title>Debuggy Gauger report</title></head><body><pre>' . $info . '</pre></body></html>';
	}


	/**
	 * Whether do enclosing in html context
	 *
	 * @var bool
	 */
	private $_encloseFlag;
}