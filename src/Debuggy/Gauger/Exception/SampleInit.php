<?php


namespace Debuggy\Gauger\Exception;


use Debuggy\Gauger\Exception;

use InvalidArgumentException;



/** Construction of a sample is impossible */
class SampleInit extends InvalidArgumentException implements Exception {}