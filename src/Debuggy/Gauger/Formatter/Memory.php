<?php


namespace Debuggy\Gauger\Formatter;


use Debuggy\Gauger\Formatter;



/**
 * Formatter for memory values
 */
class Memory extends Formatter {
	/** IEC standarded (the base is 1024) */
	const IEC = -256;


	/** Metric standarded (the base is 1000) */
	const MET = 255;


	/** Kibibyte */
	const KiB = -2;


	/** Kilobyte */
	const KB = 1;


	/** Mebibyte */
	const MiB = -3;


	/** Megabyte */
	const MB = 2;


	/** Gibibyte */
	const GiB = -5;


	/** Gigabyte */
	const GB = 4;


	/** Tebibyte */
	const TiB = -9;


	/** Terabyte */
	const TB = 8;


	/** Pebibyte */
	const PiB = -17;


	/** Petabyte */
	const PB = 16;


	/** Exbibyte */
	const EiB = -33;


	/** Exabyte */
	const EB = 32;


	/** Zebibyte */
	const ZiB = -65;


	/** Zettabyte */
	const ZB = 64;


	/** Yobibyte */
	const YiB = -129;


	/** Yottabyte */
	const YB = 128;



	/**
	 * Initializes the object with the bitmask of multiples that should be used
	 * while formatting the result.
	 * The second param means whether the BCMath library to be used for
	 * calculations. If it is null it'll be figured out whether it's available.
	 *
	 * @param int $multiples Bitwise mask of multiples (IEC by default)
	 * @param bool $bcMath Whether to use BCMath or not
	 */
	public function __construct ($multiples = -256, $bcMath = null) {
		if (is_array ($multiples) && isset ($multiples[0])) {
			$multiples = array_reduce (
				array_slice ($multiples, 1),
				$multiples[0] > 0
					? function ($s, $v) {return $s | $v;}
					: function ($s, $v) {return $s & $v;},
				$multiples[0]
			);

		} else if (!in_array ($multiples, array (self::IEC, self::MET)))
			$multiples = self::IEC;

		$this->_multiples = $multiples;

		if (!isset ($bcMath))
			$this->_bcMath = function_exists ('bcmod') && function_exists ('bcdiv');

		else
			$this->_bcMath = $bcMath;
	}


	/** {@inheritdoc} */
	public function format ($value) {
		$result = array ();

		foreach ($this->_bases as $multiple => $baseData) {
			if ($multiple !== 0 && ($multiple < 0 xor $this->_multiples < 0))
				continue;

			else if ($multiple < 0 && ($this->_multiples & $multiple) !== $this->_multiples)
				continue;

			else if ($multiple >= 0 && ($this->_multiples & $multiple) !== $multiple)
				continue;

			if ($this->_bcMath)
				$calc = floor (bcdiv ((string) ($value), $baseData['base']));

			else
				$calc = floor ($value / $baseData['base']);

			if ($calc) {
				if ($this->_bcMath)
					$value = bcmod ((string) ($value), $baseData['base']);

				else
					$value = fmod ($value, $baseData['base']);

				$result[] = sprintf ('%d%s', $calc, $baseData['name']);
			}
		}

		return implode (' ', $result);
	}



	/**
	 * Mask of multiples to calculate
	 *
	 * @var int
	 */
	private $_multiples;


	/**
	 * Whether to use libbcmath for calculations
	 *
	 * @var bool
	 */
	private $_bcMath;


	/**
	 * Map of multiples and their bases
	 *
	 * @var array
	 */
	private $_bases = array (
		-129 => array (
			'base' => '1208925819614629174706176',
			'name' => 'YiB'
		),
		128 => array (
			'base' => '1000000000000000000000000',
			'name' => 'YB'
		),

		-65 => array (
			'base' => '1180591620717411303424',
			'name' => 'ZiB'
		),
		64 => array (
			'base' => '1000000000000000000000',
			'name' => 'ZB'
		),

		-33 => array (
			'base' => '1152921504606846976',
			'name' => 'EiB'
		),
		32 => array (
			'base' => '1000000000000000000',
			'name' => 'EB'
		),

		-17 => array (
			'base' => '1125899906842624',
			'name' => 'PiB'
		),
		16 => array (
			'base' => '1000000000000000',
			'name' => 'PB'
		),

		-9 => array (
			'base' => '1099511627776',
			'name' => 'TiB'
		),
		8 => array (
			'base' => '1000000000000',
			'name' => 'TB'
		),

		-5 => array (
			'base' => '1073741824',
			'name' => 'GiB'
		),
		4 => array (
			'base' => '1000000000',
			'name' => 'GB'
		),

		-3 => array (
			'base' => '1048576',
			'name' => 'MiB'
		),
		2 => array (
			'base' => '1000000',
			'name' => 'MB'
		),

		-2 => array (
			'base' => '1024',
			'name' => 'KiB'
		),
		1 => array (
			'base' => '1000',
			'name' => 'KB'
		),

		0 => array (
			'base' => '1',
			'name' => 'B'
		)
	);
}