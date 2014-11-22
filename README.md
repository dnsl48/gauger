Gauger
======

## [![Build Status](https://travis-ci.org/dnsl48/gauger.svg?branch=master)](https://travis-ci.org/dnsl48/Gauger)  [![Coverage Status](https://img.shields.io/coveralls/dnsl48/Gauger.svg)](https://coveralls.io/r/dnsl48/Gauger?branch=master) [![License](https://poser.pugx.org/debuggy/gauger/license.svg)](https://packagist.org/packages/debuggy/gauger)

Tests were passing PHP 5.3, 5.4, 5.5, 5.6 and hhvm.

What is Gauger?
---------------

Gauger is an extensible and comprehensive tool for measuring PHP scripts and applications. It is written rather for debugging, however it could be used for testing purposes as well.


Features
--------

 * Two built-in indicators: Elapsed time (`microtime`) / Used memory (`memory_get_usage`)
 * Extremely easy way to make your own indicators (`Closure`, `Gauger::getGauge`)
 * Built-in calculation of a summary info (see example)
 * Easy way to gauge with a correct exceptions handling (`Gauger::gauge`)
 * Most critical code can be gauged without any overhead (`Gauger::stamp`)
 * Any custom details can be kept during gauges and obtained later (`Mark::extra`)
 * Static access to any gauger by its name in any place of an Application (`Gauger::getStatic`)
 * Flexible filtering of the most interesting parts of statistic (`Filter`)


Installation
------------

There are two ways to install Gauger into your project: `composer` and `manual`.

### Composer

To install the library with composer you should put the next code into your project's composer.json:

```json
{
	"require-dev": {
		"debuggy/gauger": "0.9"
	}
}
```
To see how to use composer in general, you can look at the [official documentation](https://getcomposer.org/).


### Manual installation

You can clone the repository of the library from github. Only what you need is autoloader which is compatible with `PSR-4` standard.
The library's root namespace is `Debuggy` that is in the folder `src`.


Documentation
-------------

The project's documentation is comments in the source code. Please, feel free to read them.

However, here are some basic concepts:

 * `Mark`: [Business Object](http://en.wikipedia.org/wiki/Business_object) with gathered info
 * `Gauger`: Makes marks and keeps them
 * `Reporter`: Transforms marks into reports (text, html, xml, php-array and others)
 * `Filter`: Gaugers use filters to decide whether marks should be kept


Examples
--------

Though you can make Gauger's derived class that will take any measures you need, for now there are
two base gaugers you can use.

 * `StretchAccumulator`: Accumulates all marks during its work and produces two collections of marks: Sequential and Summary
 * `StretchCalculator`: Does not keep all regular marks during the work. Eventually produces only Summary collection of marks

Lets see the example with `StretchAccumulator`:

```php
<?php
include __DIR__.'/vendor/autoload.php';

use Debuggy\Gauger\StretchTimeAccumulator;

$gauger = new StretchTimeAccumulator ('md5 vs sha1');

for ($i = 0; $i < 2; ++$i) {
	$gauger->mark ('md5');
	md5 ('text');
	$gauger->mark ('md5');

	$gauger->mark ('sha1');
	sha1 ('text');
	$gauger->mark ('sha1');
}

echo $gauger;
```

The result will be like that:

```
********************************* md5 vs sha1 **********************************
*********************************** Regular ************************************
* 1. md5 ............................................................ 0.000020 *
* 2. sha1 ........................................................... 0.000013 *
* 3. md5 ............................................................ 0.000009 *
* 4. sha1 ........................................................... 0.000009 *
*********************************** Summary ************************************
* md5 (2) ........................................................... 0.000029 *
* sha1 (2) .......................................................... 0.000022 *
********************************************************************************
```

Here we can see, that md5 was slower in the Summary, however in the second loop it took the same time as sha1.
Also, in the Summary we can see that each marker was harvested twice (embraced figure).


License
-------
`Apache License, Version 2.0`