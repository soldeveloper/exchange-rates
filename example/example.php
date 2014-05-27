<?php

/**
 * @category		SolDeveloper
 * @package		ExchangeRates
 * @author		Sol Developer <sol.developer@gmail.com>
 * @copyright		Copyright (c) 2014 Sol Developer (https://github.com/soldeveloper/exchange-rates)
 * @license		http://www.gnu.org/copyleft/lesser.html
 * @sample
 */

require_once 'bootstrap.php';

use ExchangeRates\Exception;
use ExchangeRates\Conversion;
use ExchangeRates\Currency;

try
{
	$conversion = Conversion::factory('bank.gov.ua');
	$cash = $conversion->convert(50, Currency::USD, Currency::EUR);
	echo PHP_EOL . 'Convert 50USD from EUR: ' . $cash . PHP_EOL;
	echo PHP_EOL . 'Convert ' . $cash . 'EUR from USD: ' . $conversion->convert($cash, Currency::EUR, Currency::USD);
	echo PHP_EOL . PHP_EOL;
	echo $conversion->getJavaScriptAPI();
	echo PHP_EOL . PHP_EOL;
}
catch (Exception $exception)
{
	echo $exception->getMessage() . PHP_EOL;
	echo $exception->getFile() . ' : ' . $exception->getCode() . PHP_EOL;
}
