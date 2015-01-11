<?php


require_once __DIR__ . '/Gauger.php';


require_once __DIR__ . '/Gauger/Reporter.php';
require_once __DIR__ . '/Gauger/Reporter/Html.php';
require_once __DIR__ . '/Gauger/Reporter/Txt.php';
require_once __DIR__ . '/Gauger/Reporter/PhpArray.php';

require_once __DIR__ . '/Gauger/Reporter/Formatter.php';
require_once __DIR__ . '/Gauger/Reporter/Formatter/Closure.php';
require_once __DIR__ . '/Gauger/Reporter/Formatter/Memory.php';
require_once __DIR__ . '/Gauger/Reporter/Formatter/Microtime.php';


require_once __DIR__ . '/Gauger/Filter/Sequential.php';
require_once __DIR__ . '/Gauger/Filter/Summary.php';


require_once __DIR__ . '/Gauger/Filter/SequentialClosure.php';
require_once __DIR__ . '/Gauger/Filter/SummaryClosure.php';
require_once __DIR__ . '/Gauger/Filter/Between.php';


require_once __DIR__ . '/Gauger/Mark.php';


require_once __DIR__ . '/Gauger/Mark/Summary.php';
require_once __DIR__ . '/Gauger/Mark/Sequential.php';

require_once __DIR__ . '/Gauger/StretchCalculator.php';
require_once __DIR__ . '/Gauger/StretchAccumulator.php';


require_once __DIR__ . '/Gauger/StretchTimeCalculator.php';
require_once __DIR__ . '/Gauger/StretchTimeAccumulator.php';


require_once __DIR__ . '/Gauger/StretchMemoryCalculator.php';
require_once __DIR__ . '/Gauger/StretchMemoryAccumulator.php';


require_once __DIR__ . '/Gauger/StretchClosureCalculator.php';
require_once __DIR__ . '/Gauger/StretchClosureAccumulator.php';