<?php


use Debuggy\Gauger;
use Debuggy\Gauger\StretchTimeAccumulator;
use Debuggy\Gauger\StretchTimeCalculator;

use Debuggy\Gauger\Mark;

use Debuggy\Gauger\Reporter\Txt;
use Debuggy\Gauger\Reporter\PhpArray;
use Debuggy\Gauger\Reporter\Html;

use Debuggy\Gauger\Reporter\Formatter\Closure as ClosureFormatter;
use Debuggy\Gauger\Reporter\Formatter\Memory as MemoryFormatter;
use Debuggy\Gauger\Reporter\Formatter\Microtime as MicrotimeFormatter;


class ReporterTest extends PHPUnit_Framework_TestCase {
	public function testTxt () {
		$reporter = new Txt (36);

		$gauger1 = new StretchTimeCalculator ('First');

		$gauger1->stamp (1, 'marker');
		$gauger1->stamp (2, 'marker');


		$gauger2 = new StretchTimeCalculator ('Second');

		$gauger2->stamp (0.1, 'marker');
		$gauger2->stamp (0.2, 'marker');

		$expectedResult =
			'************** First ***************'.PHP_EOL.
			'************* Summary **************'.PHP_EOL.
			'* marker (1) ................... 1 *'.PHP_EOL.
			'************************************'.PHP_EOL.
			'************** Second **************'.PHP_EOL.
			'************* Summary **************'.PHP_EOL.
			'* marker (1) ................. 0.1 *'.PHP_EOL.
			'************************************'.PHP_EOL;

		$this->assertEquals ($expectedResult, $reporter->gaugers (array ($gauger1, $gauger2)));

		$m = new Mark;
		$m->marker = 'Marker';
		$m->gauge = '0.000100';

		$this->assertEquals ('* Marker ................ 0.000100 *', $reporter->mark ($m));


		$gauger = new StretchTimeAccumulator ('Very very long long string string');
		$gauger->stamp (1, 'marker');
		$gauger->stamp (2, 'marker');
		$reporter->setGaugeFormatter (new ClosureFormatter (function () {return '0.000001';}));

		$expectedResult =
			'********** Very very long **********'.PHP_EOL.
			'*********** long string ************'.PHP_EOL.
			'************** string **************'.PHP_EOL.
			'************* Regular **************'.PHP_EOL.
			'* 1. marker ............. 0.000001 *'.PHP_EOL.
			'************* Summary **************'.PHP_EOL.
			'* marker (1) ............ 0.000001 *'.PHP_EOL.
			'************************************'.PHP_EOL;

		$this->assertEquals ($expectedResult, $reporter->gauger ($gauger));


		$gauger = new StretchTimeAccumulator ('_');
		$gauger->stamp (1, 'long marker');
		$gauger->stamp (2, 'long marker', array ('extra key' => 'long string'));
		$reporter->setGaugeFormatter (null);

		$expectedResult =
			'**************** _ *****************'.PHP_EOL.
			'************* Regular **************'.PHP_EOL.
			'* 1. long                          *'.PHP_EOL.
			'* marker ....................... 1 *'.PHP_EOL.
			'** former ....................... **'.PHP_EOL.
			'** latter                         **'.PHP_EOL.
			'*** extra key ............. long ***'.PHP_EOL.
			'***                       string ***'.PHP_EOL.
			'************* Summary **************'.PHP_EOL.
			'* long                             *'.PHP_EOL.
			'* marker (1) ................... 1 *'.PHP_EOL.
			'************************************'.PHP_EOL;

		$this->assertEquals ($expectedResult, $reporter->gauger ($gauger));
	}

	public function testHtml () {
		$reporter = new Html (true, 36);

		$gauger1 = new StretchTimeCalculator ('First');

		$gauger1->stamp (1, 'marker');
		$gauger1->stamp (2, 'marker');


		$gauger2 = new StretchTimeCalculator ('Second');

		$gauger2->stamp (0.1, 'marker');
		$gauger2->stamp (0.2, 'marker');

		$expectedResult =
			'<!DOCTYPE html><html><head><title>Debuggy Gauger report</title></head><body><pre>************** First ***************'.PHP_EOL.
			'************* Summary **************'.PHP_EOL.
			'* marker (1) ................... 1 *'.PHP_EOL.
			'************************************'.PHP_EOL.
			'************** Second **************'.PHP_EOL.
			'************* Summary **************'.PHP_EOL.
			'* marker (1) ................. 0.1 *'.PHP_EOL.
			'************************************'.PHP_EOL.
			'</pre></body></html>';

		$this->assertEquals ($expectedResult, $reporter->gaugers (array ($gauger1, $gauger2)));


		$gauger = new StretchTimeCalculator ('First');

		$gauger->stamp (1, 'marker');
		$gauger->stamp (2, 'marker');

		$expectedResult =
			'<!DOCTYPE html><html><head><title>Debuggy Gauger report</title></head><body><pre>************** First ***************'.PHP_EOL.
			'************* Summary **************'.PHP_EOL.
			'* marker (1) ................... 1 *'.PHP_EOL.
			'************************************'.PHP_EOL.
			'</pre></body></html>';

		$this->assertEquals ($expectedResult, $reporter->gauger ($gauger1));


		$marks = $gauger->getMarks ();

		$expectedResult =
			'<!DOCTYPE html><html><head><title>Debuggy Gauger report</title></head><body><pre>************* Summary **************'.PHP_EOL.
			'* marker (1) ................... 1 *'.PHP_EOL.
			'************************************'.PHP_EOL.
			'</pre></body></html>';

		$this->assertEquals ($expectedResult, $reporter->marks ($marks));


		$expectedResult =
			'<!DOCTYPE html><html><head><title>Debuggy Gauger report</title></head><body><pre>'.
			'* marker (1) ................... 1 *'.
			'</pre></body></html>';

		$this->assertEquals ($expectedResult, $reporter->mark ($marks['marker']));
	}

	public function testPhpArray () {
		$reporter = new PhpArray;

		$gauger1 = new StretchTimeAccumulator ('First');

		$gauger1->stamp (1, 'marker');
		$gauger1->stamp (2, 'marker');


		$gauger2 = new StretchTimeAccumulator ('Second');

		$gauger2->stamp (0.1, 'marker');
		$gauger2->stamp (0.2, 'marker', array ('one' => 1));

		$expectedResult = array (
			'First' => array (
				0 => array (
					'marker' => 'marker',
					'gauge' => 1,
					'extra' => NULL,
					'number' => 1
				),
				'marker' => array (
					'marker' => 'marker',
					'gauge' => 1,
					'extra' => NULL,
					'count' => 1
				)
			),
			'Second' => array (
				0 => array (
					'marker' => 'marker',
					'gauge' => 0.1,
					'extra' => array ('former' => null, 'latter' => array ('one' => 1)),
					'number' => 1
				),
				'marker' => array (
					'marker' => 'marker',
					'gauge' => 0.1,
					'extra' => null,
					'count' => 1
				)
			)
		);

		$this->assertEquals ($expectedResult, $reporter->gaugers (array ($gauger1, $gauger2)));
	}


	public function testMemoryFormatter () {
		$formatter = new MemoryFormatter;

		if (function_exists ('bcdiv') && function_exists ('bcmod')) {
			$this->assertSame ('1KiB', $formatter->transform (1024), 'MicrotimeFormatter wrong transformation');
			$this->assertSame ('3MiB 24KiB', $formatter->transform (3170304), 'MicrotimeFormatter wrong transformation');
			$this->assertSame ('2GiB 32MiB 264KiB', $formatter->transform (2181308416), 'MicrotimeFormatter wrong transformation');
			$this->assertSame ('3TiB 2GiB 32MiB 264KiB', $formatter->transform (3300716191744), 'MicrotimeFormatter wrong transformation');
			$this->assertSame ('2PiB 3TiB 2GiB 32MiB 264KiB', $formatter->transform (2255100529876992), 'MicrotimeFormatter wrong transformation');
			$this->assertSame ('4EiB 2PiB 3TiB 2GiB 32MiB 264KiB', $formatter->transform (4613941118957264896), 'MicrotimeFormatter wrong transformation');
			$this->assertSame ('3ZiB 4EiB 2PiB 3TiB 2GiB 32MiB 264KiB', $formatter->transform ('3546388803271191175168'), 'MicrotimeFormatter wrong transformation');
			$this->assertSame ('86YiB 3ZiB 4EiB 2PiB 3TiB 2GiB 32MiB 264KiB', $formatter->transform ('103971166875661380215906304'), 'MicrotimeFormatter wrong transformation');
		}

		$formatter->useBcMath (false);

		$this->assertSame ('1KiB', $formatter->transform (1024), 'MicrotimeFormatter wrong transformation');
		$this->assertSame ('3MiB 24KiB', $formatter->transform (3170304), 'MicrotimeFormatter wrong transformation');
		$this->assertSame ('2GiB 32MiB 264KiB', $formatter->transform (2181308416), 'MicrotimeFormatter wrong transformation');
		$this->assertSame ('3TiB 2GiB 32MiB 264KiB', $formatter->transform (3300716191744), 'MicrotimeFormatter wrong transformation');
	}


	public function testMicrotimeFormatter () {
		$formatter = new MicrotimeFormatter;
		$this->assertSame ('1.010000', $formatter->transform (1.01), 'MicrotimeFormatter wrong transformation');
	}
}