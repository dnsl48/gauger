<?php


namespace Debuggy\Gauger\Presenter;


use Debuggy\Gauger\Presenter;



/**
 * Makes a text representation of the reported data
 */
class Txt implements Presenter {
	/**
	 * Initializes the object with a Gauge and some output parameters like output width and text symbols
	 *
	 * @param int $outputWidth Width of output message
	 * @param string $border Character to build the border of the message
	 * @param string $filler Character to fill space between a stamp identifier and its value
	 */
	public function __construct ($outputWidth = 80, $border = '*', $filler = '.') {
		$this->_outputWidth = $outputWidth;
		$this->_border = $border;
		$this->_filler = $filler;
	}



	/** {@inheritdoc} */
	public function represent (array $data, $depth = 1) {
		$result = array ();

		foreach ($data as $key => $value) {
			if (is_int ($key) && is_array ($value) && count ($value) === 2) {
				$result[] = $this->_report (
					$this->_justify ($this->_array ($value[0])),
					$this->_justify ($this->_array ($value[1])),
					$this->_filler,
					$this->_border,
					$this->_outputWidth
				) . PHP_EOL;

			} else if (is_string ($key)) {
				$result[] = $this->_title ($this->_space ($key), $this->_border, $this->_outputWidth) . PHP_EOL;
				$result[] = $this->represent ($value, 0);

			} else if (is_string ($value))
				$result[] = $this->_justify (
					$this->_wrap (
						$value,
						$this->_outputWidth - $this->_strlen ($this->_border) * 2 - 2
					),
					$this->_outputWidth,
					$this->_border
				) . PHP_EOL;

			else
				$result[] = $this->represent ($value, $depth + 1);
		}

		$result = implode ('', $result);

		if ($depth === 0)
			$result .= $this->_title ('', $this->_border, $this->_outputWidth) . PHP_EOL . PHP_EOL;

		return $result;
	}


	/**
	 * Workaround for utf8 strings
	 *
	 * @param string $string String to be measured
	 *
	 * @return int
	 */
	private function _strlen ($string) {
		return strlen (utf8_decode ($string));
	}


	/**
	 * Encircle value with spaces at both sides if the value is not the space itself
	 *
	 * @param string $value Value to be encircled
	 *
	 * @return string
	 */
	private function _space ($value) {
		if (!$this->_strlen ($value))
			return $value;

		if (substr ($value, 0, 1) !== ' ')
			$value = ' ' . $value;

		if (substr ($value, -1) !== ' ')
			$value .= ' ';

		return $value;
	}


	/**
	 * Justify string so that each line have the same length
	 *
	 * @param string $string String to be justified
	 * @param int $minlength Minimal length of the justified string
	 *
	 * @return string
	 */
	private function _justify ($string, $minlength = 0, $enclose = null) {
		$lines = explode (PHP_EOL, $string);
		$length = $minlength;

		for ($i = 0, $c = count ($lines); $i < $c; ++$i)
			$length = max ($length, $this->_strlen ($lines[$i]));

		for ($i = 0, $c = count ($lines); $i < $c; ++$i) {
			if ($enclose)
				$lines[$i] = $enclose . ' ' . str_pad ($lines[$i], $length - $this->_strlen ($enclose) * 2 - 2) . ' ' . $enclose;
			else
				$lines[$i] = str_pad ($lines[$i], $length);
		}

		return implode (PHP_EOL, $lines);
	}


	/**
	 * Converts an array into a string
	 *
	 * @param mixed $value Value to be converted
	 * @param int $depth Recursion depth for indentation
	 *
	 * @return string
	 */
	private function _array ($value, $depth = 0) {
		if (is_scalar ($value))
			return str_pad ($value, $this->_strlen ($value) + $depth, ' ', STR_PAD_LEFT);

		if (is_object ($value))
			$value = (array) $value;

		$result = array ();

		foreach ($value as $key => $val) {
			if (is_int ($key)) {
				if (is_array ($val))
					$result[] = $this->_array ($val, $depth + 1);
				else
					$result[] = $this->_array ($val, $depth);
			}

			else if (!is_array ($val))
				$result[] = $this->_array (($key . ': ' . $val), $depth);

			else
				$result[] = str_pad ($v = ($key . ': ' . PHP_EOL . $this->_array ($val, $depth + 1)), $this->_strlen ($v) + $depth, ' ', STR_PAD_LEFT);
		}

		return implode (PHP_EOL, $result);
	}


	/**
	 * Wraps the string.
	 * Built-in function wordwrap damages a string sometimes without any tangible causes (php5.6.4)
	 *
	 * @param string $string String to be wrapped
	 * @param int $width Max width of a line
	 * @param string $break Break symbol
	 *
	 * @return string
	 */
	private function _wrap ($string, $width, $break = PHP_EOL) {
		if ($width <= 1)
			$width = $this->_strlen ($break) + 1;

		if ($width <= $this->_strlen ($break))
			$break = PHP_EOL;


		$breakLength = $this->_strlen ($break);

		$lines = explode (PHP_EOL, $string);

		$glue = array ();

		for ($i = 0; $i < count ($lines); ++$i) {
			if ($this->_strlen ($lines[$i]) > $width) {
				$line = $lines[$i];

				$breakPoint = strrpos (substr ($line, 0, $width - $breakLength), ' ');

				if ($breakPoint !== false)
					$breakPoint2 = $breakPoint + 1;
				else
					$breakPoint2 = $breakPoint = $width - $breakLength;

				array_splice ($lines, $i, 1, array (substr ($line, 0, $breakPoint) . $break, substr ($line, $breakPoint2)));
				$glue[] = $i + 1;
			}
		}

		for ($i = count ($lines); $i >= 0; --$i) {
			if (in_array ($i, $glue))
				array_splice ($lines, $i - 1, 2, array ($lines[$i - 1] . $lines[$i]));
		}

		return implode (PHP_EOL, $lines);
	}


	/**
	 * Generates a title line with the $string at the center and the
	 * both sides filled with the $filler. Fixed length of the line is $strlen.
	 * Mimimal $strlen is 4.
	 *
	 * @param string $string Message of the line
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

		if ($this->_strlen ($string) > floor ($strlen / 2)) {
			$string = $this->_wrap ($string, floor ($strlen / 2) - 1, ' '.PHP_EOL.' ');
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
			$flen = floor (($strlen - $this->_strlen ($string)) / 2 / $this->_strlen ($filler));
			$flen = $flen > 0 ? $flen : 0;

			$restlen = floor (($strlen - ($flen * 2 * $this->_strlen ($filler) + $this->_strlen ($string))) / $this->_strlen ($filler));
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
	 * Generates a report line from the passed data. Left part is the clause name and can not be greater
	 * than 1/3 of the full length of the string. Right part is the data and can not be greater than
	 * 1/2 of the full length of the string. Filler is the part between the left and the right -
	 * it fills the line so that it will be as long as needed to be equal to $strlen. Strlen can not be lesser
	 * than 10 symbols.
	 *
	 * @param string $left Left part to display
	 * @param string $right Right part to display
	 * @param string $filler Filler between the left and the right
	 * @param string $border The border symbol
	 * @param int $strlen Length of the string
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

		if ($this->_strlen ($left) > floor ($strlen / 3)) {
			$left = $this->_wrap ($left, floor ($strlen / 3) - 1, ' '.PHP_EOL.' ');
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

			if ($this->_strlen ($right) > floor ($strlen / 2)) {
				$right = $this->_wrap ($right, floor ($strlen / 2) - 2, ' '.PHP_EOL.' ');
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

				$flen = $strlen - $this->_strlen ($border) * 2 - $this->_strlen ($left) - $this->_strlen ($right);
				$flen = $flen > 0 ? $flen : 0;

				return $border . ' ' . $left . ' ' . str_repeat ($filler, $flen - 4) . ' ' . $right . ' ' . $border;

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
	 * Width of the output message
	 *
	 * @var int
	 */
	private $_outputWidth;


	/**
	 * Symbol for a message border generation
	 *
	 * @var string
	 */
	private $_border;


	/**
	 * Symbol to fill space between a stamp id and its value
	 *
	 * @var string
	 */
	private $_filler;
}