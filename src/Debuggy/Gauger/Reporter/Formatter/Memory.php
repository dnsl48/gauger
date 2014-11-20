<?php


namespace Debuggy\Gauger\Reporter\Formatter;


use Debuggy\Gauger\Reporter\Formatter;



/**
 * Transforms number of bytes to human readable representation
 */
class Memory extends Formatter {
	/**
	 * Kibibyte
	 */
	const KiB = -2;


	/**
	 * Kilobyte
	 */
	const KB = 1;


	/**
	 * Mebibyte
	 */
	const MiB = -3;


	/**
	 * Megabyte
	 */
	const MB = 2;


	/**
	 * Gibibyte
	 */
	const GiB = -5;


	/**
	 * Gigabyte
	 */
	const GB = 4;


	/**
	 * Tebibyte
	 */
	const TiB = -9;


	/**
	 * Terabyte
	 */
	const TB = 8;


	/**
	 * Pebibyte
	 */
	const PiB = -17;


	/**
	 * Petabyte
	 */
	const PB = 16;


	/**
	 * Exbibyte
	 */
	const EiB = -33;


	/**
	 * Exabyte
	 */
	const EB = 32;


	/**
	 * Zebibyte
	 */
	const ZiB = -65;


	/**
	 * Zettabyte
	 */
	const ZB = 64;


	/**
	 * Yobibyte
	 */
	const YiB = -129;


	/**
	 * Yottabyte
	 */
	const YB = 128;


	/**
	 * IEC standarded (the base is 1024)
	 */
	const IEC = -256;


	/**
	 * Metric standarded (the base is 1000)
	 */
	const MET = 255;


	/**
	 * Initializes list of multiples to calculate.
	 *
	 * @param int $multiples List of multiples as bitwise mask (IEC by default)
	 */
	public function __construct ($multiples = -256) {
		$this->_multiples = $multiples;

		/* lets detect it automatically */
		$this->useBcMath (null);
	}


	/**
	 * Enables or disables usage of libbcmath for calculations
	 */
	public function useBcMath ($flag = null) {
		if ($flag === null && function_exists ('bcmod') && function_exists ('bcdiv'))
			$flag = true;

		$this->_bcMath = $flag;
	}


	/** {@inheritdoc} */
	public function transform ($gauge) {
		$result = array ();

		foreach (static::$_bases as $multiple => $baseData) {
			if ($multiple < 0 && ($this->_multiples & $multiple) !== $this->_multiples)
				continue;
			else if ($multiple >= 0 && ($this->_multiples & $multiple) !== $multiple)
				continue;

			if ($this->_bcMath)
				$calc = floor (bcdiv ($gauge, $baseData['base']));
			else
				$calc = floor ($gauge / $baseData['base']);

			if ($calc) {
				if ($this->_bcMath)
					$gauge = bcmod ($gauge, $baseData['base']);
				else
					$gauge = fmod ($gauge, $baseData['base']);

				$result[] = sprintf ('%d%s', $calc, $baseData['name']);
			}
		}

		return implode (' ', $result);
	}


	/**
	 * List of multiples to calculate
	 *
	 * @var int
	 */
	private $_multiples;


	/**
	 * Whether use libbcmath for calculations
	 *
	 * @var bool
	 */
	private $_bcMath;


	/**
	 * Map of precalculated values for each multiple
	 *
	 * @var array
	 */
	private static $_bases = array (
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