<?php


use Debuggy\Gauger;
use Debuggy\Gauger\StretchAccumulator;
use Debuggy\Gauger\StretchCalculator;

use Debuggy\Gauger\Mark;

use Debuggy\Gauger\Formatter\Txt;
use Debuggy\Gauger\Formatter\PhpArray;
use Debuggy\Gauger\Formatter\Html;


class FormatterTest extends PHPUnit_Framework_TestCase {
	public function testTxt () {
		$formatter = new Txt (36);

		$gauger1 = new StretchCalculator ('First');

		$gauger1->stamp (1, 'marker');
		$gauger1->stamp (2, 'marker');


		$gauger2 = new StretchCalculator ('Second');

		$gauger2->stamp (0.1, 'marker');
		$gauger2->stamp (0.2, 'marker');

		$expectedResult =
			'************** First ***************'.PHP_EOL.
			'************* Summary **************'.PHP_EOL.
			'* marker (1) ............ 1.000000 *'.PHP_EOL.
			'************************************'.PHP_EOL.
			'************** Second **************'.PHP_EOL.
			'************* Summary **************'.PHP_EOL.
			'* marker (1) ............ 0.100000 *'.PHP_EOL.
			'************************************'.PHP_EOL;

		$this->assertEquals ($expectedResult, $formatter->gaugers (array ($gauger1, $gauger2)));

		$m = new Mark;
		$m->marker = 'Marker';
		$m->duration = '0.000100';

		$this->assertEquals ('* Marker ................ 0.000100 *', $formatter->singleMark ($m));


		$gauger = new StretchAccumulator ('Very very long long string string');
		$gauger->stamp (1, 'marker');
		$gauger->stamp (2, 'marker');
		$formatter->setDurationHandler (function () {return '0.000001';});

		$expectedResult =
			'********** Very very long **********'.PHP_EOL.
			'*********** long string ************'.PHP_EOL.
			'************** string **************'.PHP_EOL.
			'************* Regular **************'.PHP_EOL.
			'* 1. marker ............. 0.000001 *'.PHP_EOL.
			'************* Summary **************'.PHP_EOL.
			'* marker (1) ............ 0.000001 *'.PHP_EOL.
			'************************************'.PHP_EOL;

		$this->assertEquals ($expectedResult, $formatter->gauger ($gauger));


		$gauger = new StretchAccumulator ('_');
		$gauger->stamp (1, 'long marker');
		$gauger->stamp (2, 'long marker', array ('extra key' => 'long string'));
		$formatter->setDurationHandler (null);

		$expectedResult =
			'**************** _ *****************'.PHP_EOL.
			'************* Regular **************'.PHP_EOL.
			'* 1. long                          *'.PHP_EOL.
			'* marker ................ 1.000000 *'.PHP_EOL.
			'** former ....................... **'.PHP_EOL.
			'** latter                         **'.PHP_EOL.
			'*** extra key ............. long ***'.PHP_EOL.
			'***                       string ***'.PHP_EOL.
			'************* Summary **************'.PHP_EOL.
			'* long                             *'.PHP_EOL.
			'* marker (1) ............ 1.000000 *'.PHP_EOL.
			'************************************'.PHP_EOL;

		$this->assertEquals ($expectedResult, $formatter->gauger ($gauger));
	}

	public function testHtml () {
		$formatter = new Html (true, 36);

		$gauger1 = new StretchCalculator ('First');

		$gauger1->stamp (1, 'marker');
		$gauger1->stamp (2, 'marker');


		$gauger2 = new StretchCalculator ('Second');

		$gauger2->stamp (0.1, 'marker');
		$gauger2->stamp (0.2, 'marker');

		$expectedResult =
			'<!DOCTYPE html><html><head><title>Debuggy Gauger report</title></head><body><pre>************** First ***************'.PHP_EOL.
			'************* Summary **************'.PHP_EOL.
			'* marker (1) ............ 1.000000 *'.PHP_EOL.
			'************************************'.PHP_EOL.
			'************** Second **************'.PHP_EOL.
			'************* Summary **************'.PHP_EOL.
			'* marker (1) ............ 0.100000 *'.PHP_EOL.
			'************************************'.PHP_EOL.
			'</pre></body></html>';

		$this->assertEquals ($expectedResult, $formatter->gaugers (array ($gauger1, $gauger2)));


		$gauger = new StretchCalculator ('First');

		$gauger->stamp (1, 'marker');
		$gauger->stamp (2, 'marker');

		$expectedResult =
			'<!DOCTYPE html><html><head><title>Debuggy Gauger report</title></head><body><pre>************** First ***************'.PHP_EOL.
			'************* Summary **************'.PHP_EOL.
			'* marker (1) ............ 1.000000 *'.PHP_EOL.
			'************************************'.PHP_EOL.
			'</pre></body></html>';

		$this->assertEquals ($expectedResult, $formatter->gauger ($gauger1));


		$marks = $gauger->getMarks ();

		$expectedResult =
			'<!DOCTYPE html><html><head><title>Debuggy Gauger report</title></head><body><pre>************* Summary **************'.PHP_EOL.
			'* marker (1) ............ 1.000000 *'.PHP_EOL.
			'************************************'.PHP_EOL.
			'</pre></body></html>';

		$this->assertEquals ($expectedResult, $formatter->arrayOfMarks ($marks));


		$expectedResult =
			'<!DOCTYPE html><html><head><title>Debuggy Gauger report</title></head><body><pre>'.
			'* marker (1) ............ 1.000000 *'.
			'</pre></body></html>';

		$this->assertEquals ($expectedResult, $formatter->singleMark ($marks['marker']));
	}

	public function testPhpArray () {
		$formatter = new PhpArray;

		$gauger1 = new StretchAccumulator ('First');

		$gauger1->stamp (1, 'marker');
		$gauger1->stamp (2, 'marker');


		$gauger2 = new StretchAccumulator ('Second');

		$gauger2->stamp (0.1, 'marker');
		$gauger2->stamp (0.2, 'marker', array ('one' => 1));

		$expectedResult = array (
			'First' => array (
				0 => array (
					'marker' => 'marker',
					'duration' => 1,
					'extra' => NULL,
					'number' => 1
				),
				'marker' => array (
					'marker' => 'marker',
					'duration' => 1,
					'extra' => NULL,
					'count' => 1
				)
			),
			'Second' => array (
				0 => array (
					'marker' => 'marker',
					'duration' => 0.1,
					'extra' => array ('former' => null, 'latter' => array ('one' => 1)),
					'number' => 1
				),
				'marker' => array (
					'marker' => 'marker',
					'duration' => 0.1,
					'extra' => null,
					'count' => 1
				)
			)
		);

		$this->assertEquals ($expectedResult, $formatter->gaugers (array ($gauger1, $gauger2)));
	}
}