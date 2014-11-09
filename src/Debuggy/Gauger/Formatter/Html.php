<?php


namespace Debuggy\Gauger\Formatter;


use Debuggy\Gauger;

use Debuggy\Gauger\Formatter;

use Debuggy\Gauger\Mark;


/**
 * Transforms marks to HTML report.
 * For report making it uses Txt formatter and then htmlspecialchars
 * for escaping its result.
 */
class Html extends Formatter {
	/**
	 * Constructs Html formatter that incapsulates a Txt one.
	 * If $enclose is true, it will make the full HTML page with <html><head> and <body> tags.
	 * Otherwise it will only escape special chars.
	 *
	 * @param bool $enclose Enclose flag (true by default)
	 * @param int $outputWidth Width for output in symbols
	 * @param string $border Symbol that will be used as border
	 * @param string $filler Symbol that will be used as filler
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