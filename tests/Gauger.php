<?php


namespace Debuggy\Gauger\Sample {
	class _TestSample {}

	abstract class _AbsTestSample extends \Debuggy\Gauger\Sample {}

	interface _IntTestSample {}

	class _EmptyTestSample extends \Debuggy\Gauger\Sample {
		public function toArray () {return array ();}
	}
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
			_Gauger::getSample ('key');
		}


		/**
		 * Tests trying to request unknown sample
		 *
		 * @expectedException Debuggy\Gauger\Exception\SampleUnknown
		 */
		public function testUnknownSample2 () {
			_Gauger::getSample ('key', '_TestSample', array ('some data'));
		}


		/**
		 * Tests trying to request unknown sample
		 *
		 * @expectedException Debuggy\Gauger\Exception\SampleUnknown
		 */
		public function testUnknownSample3 () {
			_Gauger::getSample ('key', '_AbsTestSample', array ('some data'));
		}


		/**
		 * Tests trying to request unknown sample
		 *
		 * @expectedException Debuggy\Gauger\Exception\SampleUnknown
		 */
		public function testUnknownSample4 () {
			_Gauger::getSample ('key', '_IntTestSample', array ('some data'));
		}


		/**
		 * Tests trying to pass construction parameters into a sample without a constructor
		 *
		 * @expectedException Debuggy\Gauger\Exception\SampleInit
		 */
		public function testInitSampleError () {
			_Gauger::getSample ('key', '_EmptyTestSample', array ('some data'));
		}


		/** Tests how does the Gauger keeps a sample */
		public function testKeepSample () {
			$sample = new Timer;

			_Gauger::getSample ('key', $sample);

			$this->assertSame ($sample, _Gauger::getSample ('key'));
		}


		/** Tests how does the Gauger keeps a sample */
		public function testKeepSample2 () {
			$sample = new Timer (2);

			_Gauger::getSample ('key', 'Timer', array (2));

			$this->assertEquals ($sample, _Gauger::getSample ('key'));
			$this->assertNotSame ($sample, _Gauger::getSample ('key'));
		}
	}

}