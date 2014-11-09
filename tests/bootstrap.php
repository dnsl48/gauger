<?php

/** The project implies PSR-4 autoloader, however it is not a dependency for tests running */

$d = __DIR__.'/../src/Debuggy/';
$g = $d.'Gauger/';


require_once $d.'Gauger.php';


require_once $g.'Formatter.php';
require_once $g.'Formatter/Html.php';
require_once $g.'Formatter/Txt.php';
require_once $g.'Formatter/PhpArray.php';


require_once $g.'Filter/Sequential.php';
require_once $g.'Filter/Summary.php';


require_once $g.'Filter/SequentialClosure.php';
require_once $g.'Filter/SummaryClosure.php';
require_once $g.'Filter/Between.php';


require_once $g.'Mark.php';


require_once $g.'Mark/Summary.php';
require_once $g.'Mark/Sequential.php';

require_once $g.'StretchCalculator.php';
require_once $g.'StretchAccumulator.php';


require_once $g.'StretchTimeCalculator.php';
require_once $g.'StretchTimeAccumulator.php';


require_once $g.'StretchMemoryCalculator.php';
require_once $g.'StretchMemoryAccumulator.php';


require_once $g.'StretchClosureCalculator.php';
require_once $g.'StretchClosureAccumulator.php';