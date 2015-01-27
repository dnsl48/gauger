<?php


use Debuggy\Gauger\Presenter\Txt;



/**
 * Tests the presenters of the Gauger
 */
class Presenters extends PHPUnit_Framework_TestCase {
	/**
	 * Tests Txt presenter
	 *
	 * @dataProvider textPresenterData
	 */
	public function testTxt ($length, $source, $result) {
		$pres1 = new Txt ($length);

		$this->assertEquals ($result, $pres1->represent ($source));
	}


	/**
	 * Data provider for Txt presenter
	 *
	 * @return array
	 */
	public function textPresenterData () {
		return array (
			array (
				24,
				array ('ttl' => array (array ('mark', 'value'))),
				'********* ttl **********' . PHP_EOL .
				'* mark ......... value *' . PHP_EOL .
				'************************' . PHP_EOL
			),


			array (
				24,
				array ('ttl' => array ('plain text much longer than twenty four symbols')),
				'********* ttl **********' . PHP_EOL .
				'* plain text much      *' . PHP_EOL .
				'* longer than twenty   *' . PHP_EOL .
				'* four symbols         *' . PHP_EOL .
				'************************' . PHP_EOL
			),


			array (
				24,
				array (array ('plain text')),
				'* plain text           *' . PHP_EOL
			),


			array (
				24,
				array ('ttl' => array (array ('mark', array ('sub' => 'val')))),
				'********* ttl **********' . PHP_EOL .
				'* mark ...... sub: val *' . PHP_EOL .
				'************************' . PHP_EOL
			),


			array (
				32,
			    array ('ttl' => array (array ('mark', array ('sub' => array ('value'), 'sub2' => array ('key' => 'value', 'key2' => 'value2'))))),
				'************* ttl **************' . PHP_EOL .
				'* mark ......... sub:          *' . PHP_EOL .
				'*                 value        *' . PHP_EOL .
				'*                sub2:         *' . PHP_EOL .
				'*                 key: value   *' . PHP_EOL .
				'*                 key2: value2 *' . PHP_EOL .
				'********************************' . PHP_EOL
			),


			array (
				14,
				array ('Antidisestablishmentarianism'),
				'* Antidises  *' . PHP_EOL .
				'* tablishme  *' . PHP_EOL .
				'* ntarianism *' . PHP_EOL
			),


			array (
				20,
				array ('Long title' => array (array ('key', 'val'))),
				'******* Long *******' . PHP_EOL .
				'****** title *******' . PHP_EOL .
				'* key ........ val *' . PHP_EOL .
				'********************' . PHP_EOL
			),


			array (
				20,
				array ('Long title' => array (array ('key', 'val'))),
				'******* Long *******' . PHP_EOL .
				'****** title *******' . PHP_EOL .
				'* key ........ val *' . PHP_EOL .
				'********************' . PHP_EOL
			),


			array (
				32,
				array ('title' => array (array ('very long key string', 'value'))),
				'************ title *************' . PHP_EOL .
				'* very                         *' . PHP_EOL .
				'*  long                        *' . PHP_EOL .
				'*  key                         *' . PHP_EOL .
				'*  string .............. value *' . PHP_EOL .
				'********************************' . PHP_EOL
			)

		);
	}
}