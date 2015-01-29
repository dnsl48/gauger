<?php


use Debuggy\Gauger\Formatter;
use Debuggy\Gauger\Formatter\Closure;
use Debuggy\Gauger\Formatter\Memory;
use Debuggy\Gauger\Formatter\Time;



/**
 * Tests the formatters of the Gauger
 */
class Formatters extends PHPUnit_Framework_TestCase {
	/** Tests base Formatter */
	public function testFormatter () {
		$formatter = new Formatter;

		$this->assertSame ("1", $formatter->format (1));
		$this->assertSame (array ("2"), $formatter->format (array (2)));
	}


	/** Tests Closure formatter */
	public function testClosure () {
		$formatter = new Closure (function ($v) {return number_format ($v, 2);});

		$this->assertSame ("2.00", $formatter->format (2));
	}


	/** Tests Memory formatter */
	public function testMemory () {
		$formatter1 = new Memory (Memory::IEC, false);

		$this->assertEquals ("62B", $formatter1->format (62));
		$this->assertEquals ("32KiB 62B", $formatter1->format (32830));
		$this->assertEquals ("6MiB 32KiB 62B", $formatter1->format (6324286));
		$this->assertEquals ("1GiB 6MiB 32KiB 62B", $formatter1->format (1080066110));


		$formatter2 = new Memory (Memory::MET, false);

		$this->assertEquals ("64B", $formatter2->format (64));
		$this->assertEquals ("16KB 64B", $formatter2->format (16064));
		$this->assertEquals ("18MB 16KB 64B", $formatter2->format (18016064));
		$this->assertEquals ("1GB 18MB 16KB 64B", $formatter2->format (1018016064));


		$formatter3 = new Memory (array (Memory::KiB, Memory::MiB));

		$this->assertEquals ("1064MiB 816KiB 16B", $formatter3->format (1116520464));


		$formatter4 = new Memory (array (Memory::KB, Memory::MB));

		$this->assertEquals ("1058MB 912KB 32B", $formatter4->format (1058912032));
	}


	/**
	 * Tests Memory formatter with BCMath
	 *
	 * @requires extension bcmath
	 * @depends testMemory
	 */
	public function testMemoryBc () {
		$formatter1 = new Memory (Memory::IEC);

		$this->assertEquals ("3TiB 1GiB 6MiB 32KiB 62B", $formatter1->format ("3299614949438"));
		$this->assertEquals ("6PiB 3TiB 1GiB 6MiB 32KiB 62B", $formatter1->format ("6758699056005182"));
		$this->assertEquals ("4EiB 6PiB 3TiB 1GiB 6MiB 32KiB 62B", $formatter1->format ("4618444717483393086"));
		$this->assertEquals ("3ZiB 4EiB 6PiB 3TiB 1GiB 6MiB 32KiB 62B", $formatter1->format ("3546393306869717303358"));
		$this->assertEquals ("68YiB 3ZiB 4EiB 6PiB 3TiB 1GiB 6MiB 32KiB 62B", $formatter1->format ("82210502127101653597323326"));


		$formatter2 = new Memory (Memory::MET);

		$this->assertEquals ("2TB 1GB 18MB 16KB 64B", $formatter2->format ("2001018016064"));
		$this->assertEquals ("4PB 2TB 1GB 18MB 16KB 64B", $formatter2->format ("4002001018016064"));
		$this->assertEquals ("3EB 4PB 2TB 1GB 18MB 16KB 64B", $formatter2->format ("3004002001018016064"));
		$this->assertEquals ("5ZB 3EB 4PB 2TB 1GB 18MB 16KB 64B", $formatter2->format ("5003004002001018016064"));
		$this->assertEquals ("16YB 5ZB 3EB 4PB 2TB 1GB 18MB 16KB 64B", $formatter2->format ("16005003004002001018016064"));
	}


	/** Tests Time formatter */
	public function testTime () {
		$formatter1 = new Time (2);

		$this->assertEquals (".64", $formatter1->format (0.638));
		$this->assertEquals ("04.62", $formatter1->format (4.623));
		$this->assertEquals ("02:04.58", $formatter1->format (124.58));
		$this->assertEquals ("06:02:04.58", $formatter1->format (6*60*60 + 124.58));
		$this->assertEquals ("654 06:02:04.58", $formatter1->format (654*24*60*60 + 6*60*60 + 124.58));
	}
}