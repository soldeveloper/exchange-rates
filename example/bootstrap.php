<?php

/**
 * @category		SolDeveloper
 * @package		ExchangeRates
 * @author		Sol Developer <sol.developer@gmail.com>
 * @copyright		Copyright (c) 2014 Sol Developer (https://github.com/soldeveloper/exchange-rates)
 * @license		http://www.gnu.org/copyleft/lesser.html
 * @sample
 */

ini_set('display_errors', 'on');
error_reporting(E_ALL);

set_include_path(
	dirname(__FILE__) . '/../src' . PATH_SEPARATOR . get_include_path()
);

require_once 'ExchangeRates/Exception.php';
require_once 'ExchangeRates/Currency.php';
require_once 'ExchangeRates/Conversion.php';
require_once 'ExchangeRates/Source.php';
require_once 'ExchangeRates/Source/BankGovUa.php';
