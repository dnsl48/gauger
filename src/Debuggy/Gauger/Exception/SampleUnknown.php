<?php


namespace Debuggy\Gauger\Exception;


use Debuggy\Gauger\Exception;

use DomainException;



/**
 * Trying to use an unknown sample
 */
class SampleUnknown extends DomainException implements Exception {}