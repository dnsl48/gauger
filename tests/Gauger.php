<?php


namespace Debuggy\Gauger\Sample {
	class _TestSample {}

	abstract class _AbsTestSample extends \Debuggy\Gauger\Sample {}

	interface _IntTestSample {}
}


namespace {

	use Debuggy\Gauger as _Gauger;
	use Debuggy\Gauger\Exception\SampleInit;
	use Debuggy\Gauger\Sample\Timer;



	/**
	 * Tests the Gauger
	 */
	class Gauger extends PHPUnit_Framework_TestCase {
		/**
		 * Tests trying to request unknown sample
		 *
		 * @expectedException Debuggy\Gauger\Exception\SampleUnknown
		 */
		public function testUnknownSample () {
			_Gauger::getSample (__FUNCTION__);
		}


		/**
		 * Tests trying to request unknown sample
		 *
		 * @expectedException Debuggy\Gauger\Exception\SampleUnknown
		 */
		public function testUnknownSample2 () {
			_Gauger::getSample (__FUNCTION__, '_TestSample', array ('some data'));
		}


		/**
		 * Tests trying to request unknown sample
		 *
		 * @expectedException Debuggy\Gauger\Exception\SampleUnknown
		 */
		public function testUnknownSample3 () {
			_Gauger::getSample (__FUNCTION__, '_AbsTestSample', array ('some data'));
		}


		/**
		 * Tests trying to request unknown sample
		 *
		 * @expectedException Debuggy\Gauger\Exception\SampleUnknown
		 */
		public function testUnknownSample4 () {
			_Gauger::getSample (__FUNCTION__, '_IntTestSample', array ('some data'));
		}


		/** Tests how does the Gauger keeps a sample */
		public function testKeepSample () {
			$sample = new Timer;

			_Gauger::getSample (__FUNCTION__, $sample);

			$this->assertSame ($sample, _Gauger::getSample (__FUNCTION__));
		}


		/** Tests how does the Gauger keeps a sample */
		public function testKeepSample2 () {
			$sample = new Timer (2);

			_Gauger::getSample (__FUNCTION__, 'Timer', array (2));

			$this->assertEquals ($sample, _Gauger::getSample (__FUNCTION__));
			$this->assertNotSame ($sample, _Gauger::getSample (__FUNCTION__));
		}
	}

}