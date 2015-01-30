<?php

require_once __DIR__ . '/Gauger/Dial.php';
require_once __DIR__ . '/Gauger/Exception.php';
require_once __DIR__ . '/Gauger/Filter.php';
require_once __DIR__ . '/Gauger/Formatter.php';
require_once __DIR__ . '/Gauger/Gauge.php';
require_once __DIR__ . '/Gauger/Indicator.php';
require_once __DIR__ . '/Gauger/Presenter.php';
require_once __DIR__ . '/Gauger/Refiner.php';
require_once __DIR__ . '/Gauger/Reporter.php';
require_once __DIR__ . '/Gauger/Sample.php';
require_once __DIR__ . '/Gauger/Stamp.php';

require_once __DIR__ . '/Gauger/Exception/SampleUnknown.php';

require_once __DIR__ . '/Gauger/Filter/AndFilters.php';
require_once __DIR__ . '/Gauger/Filter/Between.php';
require_once __DIR__ . '/Gauger/Filter/Closure.php';
require_once __DIR__ . '/Gauger/Filter/Distinct.php';
require_once __DIR__ . '/Gauger/Filter/Equal.php';
require_once __DIR__ . '/Gauger/Filter/Greater.php';
require_once __DIR__ . '/Gauger/Filter/GreaterOrEqual.php';
require_once __DIR__ . '/Gauger/Filter/Head.php';
require_once __DIR__ . '/Gauger/Filter/Last.php';
require_once __DIR__ . '/Gauger/Filter/Lesser.php';
require_once __DIR__ . '/Gauger/Filter/LesserOrEqual.php';
require_once __DIR__ . '/Gauger/Filter/Max.php';
require_once __DIR__ . '/Gauger/Filter/Min.php';
require_once __DIR__ . '/Gauger/Filter/NotEqual.php';
require_once __DIR__ . '/Gauger/Filter/OrFilters.php';
require_once __DIR__ . '/Gauger/Filter/Tail.php';

require_once __DIR__ . '/Gauger/Formatter/Closure.php';
require_once __DIR__ . '/Gauger/Formatter/Memory.php';
require_once __DIR__ . '/Gauger/Formatter/Stash.php';
require_once __DIR__ . '/Gauger/Formatter/Time.php';

require_once __DIR__ . '/Gauger/Indicator/Closure.php';
require_once __DIR__ . '/Gauger/Indicator/Extra.php';
require_once __DIR__ . '/Gauger/Indicator/Memory.php';
require_once __DIR__ . '/Gauger/Indicator/MemoryPeak.php';
require_once __DIR__ . '/Gauger/Indicator/MemoryUsage.php';
require_once __DIR__ . '/Gauger/Indicator/Microtime.php';
require_once __DIR__ . '/Gauger/Indicator/Preload.php';
require_once __DIR__ . '/Gauger/Indicator/TotalDuration.php';

require_once __DIR__ . '/Gauger/Presenter/Txt.php';

require_once __DIR__ . '/Gauger/Refiner/Cache.php';
require_once __DIR__ . '/Gauger/Refiner/Filter.php';
require_once __DIR__ . '/Gauger/Refiner/Root.php';
require_once __DIR__ . '/Gauger/Refiner/Stretch.php';
require_once __DIR__ . '/Gauger/Refiner/Total.php';

require_once __DIR__ . '/Gauger/Reporter/Plain.php';
require_once __DIR__ . '/Gauger/Reporter/Summary.php';

require_once __DIR__ . '/Gauger/Sample/Sample1.php';
require_once __DIR__ . '/Gauger/Sample/Preload1.php';
require_once __DIR__ . '/Gauger/Sample/Timer.php';
require_once __DIR__ . '/Gauger/Sample/Totals.php';


require_once __DIR__ . '/Gauger.php';