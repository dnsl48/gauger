<?php


namespace Debuggy;


use Debuggy\Gauger\Exception\SampleUnknown;
use Debuggy\Gauger\Exception\SampleInit;
use Debuggy\Gauger\Sample;

use ReflectionClass;
use ReflectionException;



/**
 * Provides an access to the global storage of samples
 */
class Gauger {
	/**
	 * Provides an access to the static storage of samples from the global space.
	 * The second argument should contain whether the name of a sample or an instance.
	 * If the storage already has a sample referred by the $key, the second and the third arguments are ignored.
	 *
	 * @param string $key Key of a sample for storing in the static storage
	 * @param string|Sample $sample Whether the name or an instance of a sample
	 * @param array $constructionData Data for passing into a constructor of a sample if the second argument is a string
	 *
	 * @return Sample
	 *
	 * @throws SampleUnknown if the second argument is a name of an unknown sample
	 * @throws SampleInit if the sample class doesn't have any arguments and the third argument isn't empty or if the sample throws this exception
	 */
	public static function getSample ($key, $sample = null, array $constructionData = array ()) {
		if (isset (self::$_samples[$key]))
			return self::$_samples[$key];

		if (isset ($sample) && $sample instanceof Sample)
			return self::$_samples[$key] = $sample;

		$className = '\Debuggy\Gauger\Sample\\' . $sample;

		try {
			$refl = new ReflectionClass ($className);

		} catch (ReflectionException $e) {
			throw new SampleUnknown ($className, 0, $e);
		}

		if (!$refl->isSubclassOf ('\Debuggy\Gauger\Sample') || $refl->isAbstract () || $refl->isInterface ())
			throw new SampleUnknown ($className, 0);

		try {
			return self::$_samples[$key] = $refl->newInstanceArgs ($constructionData);

		} catch (ReflectionException $e) {
			throw new SampleInit ($className, 0, $e);
		}
	}



	/**
	 * Map of initialized samples
	 *
	 * @var [string => \Debuggy\Gauger\Sample]
	 */
	private static $_samples = array ();
}