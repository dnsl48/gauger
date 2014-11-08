<?php


namespace Debuggy\Gauger\Formatter;


use Debuggy\Gauger;

use Debuggy\Gauger\Formatter;

use Debuggy\Gauger\Mark;
use Debuggy\Gauger\Mark\Sequential as SequentialMark;
use Debuggy\Gauger\Mark\Summary as SummaryMark;


/**
 * Implements logic of Info to Array transformation
 */
class Txt extends Formatter {
	/**
	 * Setup the output's width in symbols.
	 * The width can not be less than 16 symbols
	 *
	 * @param int $outputWidth Width for output in symbols
	 */
	public function __construct ($outputWidth = 80, $border = '*', $filler = '.') {
		if ($outputWidth < 16)
			$outputWidth = 16;

		$this->_outputWidth = $outputWidth;
		$this->_border = $border;
		$this->_filler = $filler;
	}


	/** {@inheritdoc} */
	public function gaugers (array $gaugers) {
		$result = array ();

		foreach ($gaugers as $gauger)
			$result[] = trim ($this->gauger ($gauger));

		return implode (PHP_EOL, $result).PHP_EOL;
	}


	/** {@inheritdoc} */
	public function gauger (Gauger $gauger) {
		$result = array ();

		$result[] = $this->_title ($this->_space ($gauger->getName ()), $this->_border, $this->_outputWidth);

		$result[] = trim ($this->arrayOfMarks ($gauger->getMarks ()));

		return implode (PHP_EOL, $result).PHP_EOL;
	}


	/** {@inheritdoc} */
	public function arrayOfMarks (array $marks) {
		$groups = $this->splitMarks ($marks);

		$result = array ();

		foreach ($groups as $title => $group) {
			if (count ($group)) {
				$result[] = $this->_title ($this->_space ($title), $this->_border, $this->_outputWidth);

				foreach ($group as $mark)
					$result[] = $this->singleMark ($mark);
			}
		}

		if (count ($result)) {
			$result[] = $this->_title ($this->_border, $this->_border, $this->_outputWidth);
			return implode (PHP_EOL, $result).PHP_EOL;
		} else
			return '';
	}


	/** {@inheritdoc} */
	public function singleMark (Mark $mark) {
		$result = $this->_report (
			$this->_space ($this->getMarkerName ($mark)),
			$this->_space ($this->formatDuration ($mark->duration)),
			$this->_filler,
			$this->_border,
			$this->_outputWidth
		);

		if ($mark->extra)
			$result .= PHP_EOL.implode (PHP_EOL, $this->_extra ($mark->extra));

		return $result;
	}


	/**
	 * Builds marker name and returns it
	 *
	 * @param Mark $mark Mark whose name should be generated
	 *
	 * @return string
	 */
	protected function getMarkerName (Mark $mark) {
		if ($mark instanceof SequentialMark)
			return $mark->number . '. ' . $mark->marker;
		else if ($mark instanceof SummaryMark)
			return $mark->marker . ' ('.$mark->count . ')';
		else
			return $mark->marker;
	}


	/**
	 * Split all marks into groups by their types
	 *
	 * @param array $marks List of marks to split
	 *
	 * @return array
	 */
	protected function splitMarks (array $marks) {
		$result = array (
			'Regular' => array (),
			'Summary' => array ()
		);

		foreach ($marks as $mark) {
			if ($mark instanceof SummaryMark)
				$result['Summary'][] = $mark;
			else
				$result['Regular'][] = $mark;
		}

		return $result;
	}


	/**
	 * Encircle value with spaces at both sides if the value isn't the space by itself
	 *
	 * @param string $value Value to encircling
	 *
	 * @return string
	 */
	private function _space ($value) {
		if (!strlen ($value))
			return $value;

		if (substr ($value, 0, 1) !== ' ')
			$value = ' '.$value;

		if (substr ($value, -1) !== ' ')
			$value .= ' ';

		return $value;
	}


	/**
	 * Build rows for Mark's extra info and returns them as array
	 *
	 * @param array $extra Extra info
	 *
	 * @return array
	 */
	private function _extra (array $extra, $level = 2) {
		$result = array ();

		if (!$extra)
			return $result;

		foreach ($extra as $key => $value) {
			if (is_array ($value)) {
				$result[] = $this->_report ($this->_space ($key), ' ', ' ', str_repeat ($this->_border, $level), $this->_outputWidth);

				foreach ($this->_extra ($value, $level + 1) as $row)
					$result[] = $row;
			} else
				$result[] = $this->_report ($this->_space ($key), $this->_space ((string) $value), $this->_filler, str_repeat ($this->_border, $level), $this->_outputWidth);
		}

		return $result;
	}


	/**
	 * Generates a title line with the string at the center and the
	 * both sides filled with filler string. Fixed length of the line is strlen.
	 * Mimimal strlen is 4.
	 *
	 * @param string $string Message for the center of the line
	 * @param string $filler Fills the sides of the line
	 * @param int $strlen Length of the line
	 *
	 * @return string
	 */
	private function _title ($string, $filler, $strlen) {
		if ($strlen < 4) 
			$strlen = 4;

		if ($string === '' || is_null ($string))
			$string = $filler;

		if ($filler === '' || is_null ($filler))
			$filler = ' ';

		if (strlen ($string) > floor ($strlen / 2)) {
			$string = wordwrap ($string, floor ($strlen / 2) - 1, ' '.PHP_EOL.' ', true);
			$isWrapped = true;

		} else 
			$isWrapped = false;

		$strings = explode (PHP_EOL, $string);

		if ($isWrapped) {
			if ($strings[0] === '  ')
				array_shift ($strings);

			if ($strings[count ($strings) - 1] === ' ')
				array_pop ($strings);
		}

		if (count ($strings) === 1) {
			$string = $strings[0];
			$flen = floor (($strlen - strlen ($string)) / 2 / strlen ($filler));
			$flen = $flen > 0 ? $flen : 0;

			$restlen = floor (($strlen - ($flen*2*strlen ($filler) + strlen ($string))) / strlen ($filler));
			$restlen = $restlen > 0 ? $restlen : 0;

			return str_repeat ($filler, $flen) . $string . str_repeat ($filler, $flen) . str_repeat ($filler, $restlen);
		} else {
			$result = array ();

			foreach ($strings as $str) {
				$result[] = $this->_title ($str, $filler, $strlen);
			}

			return implode (PHP_EOL, $result);
		}
	}

	/**
	 * Generates a report line from the passed data.
	 * Left part is the clause name and can not be lesser
	 * than 1/3 of the full length of the string.
	 * Right part is the data and can not be lesser than
	 * 1/3 of the full length of the string.
	 * Filler is the part between the left and the right
	 * and it fulfills the line so that will be as long
	 * as needed to be equal to strlen. Strlen can not be lesser
	 * than 10 symbols
	 *
	 * @param string $left Left part to display
	 * @param string $right Right part to display
	 * @param string $filler Filler between the left and the right
	 * @param string $border The border symbol
	 * @param string $strlen Length of the string
	 *
	 * @return string
	 */
	private function _report ($left, $right, $filler, $border, $strlen) {
		if ($strlen < 10)
			$strlen = 10;

		if ($left === '' || is_null ($left))
			$left = ' ';

		if ($right === '' || is_null ($right))
			$right = ' ';

		if ($filler === '' || is_null ($filler))
			$filler = ' ';

		if (strlen ($left) > floor ($strlen / 3)) {
			$left = wordwrap ($left, floor ($strlen / 3) - 1, ' '.PHP_EOL.' ', true);
			$isWrapped = true;
		} else 
			$isWrapped = false;

		$lefts = explode (PHP_EOL, $left);

		if ($isWrapped) {
			if ($lefts[0] === '  ')
				array_shift ($lefts);

			if ($lefts[count ($lefts) - 1] === ' ')
				array_pop ($lefts);
		}

		if (count ($lefts) === 1) {
			$left = $lefts[0];

			if (strlen ($right) > floor ($strlen / 3)) {
				$right = wordwrap ($right, floor ($strlen / 3) - 2, ' '.PHP_EOL.' ', true);
				$isWrapped = true;
			} else 
				$isWrapped = false;

			$rights = explode (PHP_EOL, $right);

			if ($isWrapped) {
				if ($rights[0] === '  ')
					array_shift ($rights);

				if ($rights[count ($rights) - 1] === ' ')
					array_pop ($rights);
			}

			if (count ($rights) === 1) {
				$right = $rights[0];

				$flen = $strlen - strlen ($border) * 2 - strlen ($left) - strlen ($right);
				$flen = $flen > 0 ? $flen : 0;

				return $border . $left . str_repeat ($filler, $flen) . $right . $border;
			} else {
				$result = array ();

				for ($i = 0, $c = count ($rights); $i < $c; ++$i) {
					if ($i === 0)
						$result[] = $this->_report ($left, $rights[$i], $filler, $border, $strlen);
					else
						$result[] = $this->_report (' ', $rights[$i], ' ', $border, $strlen);
				}

				return implode (PHP_EOL, $result);
			}
		} else {
			$result = array ();

			for ($i = 1, $c = count ($lefts); $i <= $c; ++$i) {
				if ($i === $c)
					$result[] = $this->_report ($lefts[$i - 1], $right, $filler, $border, $strlen);
				else
					$result[] = $this->_report ($lefts[$i - 1], ' ', ' ', $border, $strlen);
			}

			return implode (PHP_EOL, $result);
		}
	}


	/**
	 * Width of the output in symbols
	 *
	 * @var int
	 */
	private $_outputWidth;


	/**
	 * Border symbol
	 *
	 * @var string
	 */
	private $_border;


	/**
	 * Filler symbol
	 *
	 * @var string
	 */
	private $_filler;
}