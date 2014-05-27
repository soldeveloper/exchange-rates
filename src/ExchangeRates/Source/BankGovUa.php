<?php
/**
 * @category		SolDeveloper
 * @package		ExchangeRates
 * @author		Sol Developer <sol.developer@gmail.com>
 * @copyright		Copyright (c) 2014 Sol Developer (https://github.com/soldeveloper/exchange-rates)
 * @license		http://www.gnu.org/copyleft/lesser.html
 */

namespace ExchangeRates\Source;

use ExchangeRates\Source;
use ExchangeRates\Exception;
use ExchangeRates\Currency;

/**
 * Источнике данных с сайта www.bank.gov.ua.
 */
class BankGovUa extends Source
{

	/**
	 * Создает и возвращает хешмап таблицу где первое измерение это
	 * исходная валюта, второе конечная валюта, значение в ячейке - коэффициент умножения.
	 * Возвращает текущий объект.
	 *
	 * @return array
	 *
	 * @throws Exception
	 */
	protected function createMapCurrency()
	{
		$response = $this->requestHTTPClient(
			'http://www.bank.gov.ua/control/uk/curmetal/detail/currency',
			array('period' => 'daily'),
			self::HTTP_METH_GET
		);

		if (
			!is_array($response) ||
			!isset($response['content']) ||
			!isset($response['errno']) ||
			(0 != $response['errno'])
		)
		{
			throw new Exception('Error retrieving data from the source.');
		}

		$pageContent = $response['content'];

		$currencyReflectionClas = new \ReflectionClass('\ExchangeRates\Currency');
		$constants = $currencyReflectionClas->getConstants();

		/**
		 * Заполняет хешмап таблицу.
		 */
		$mapCurrency = array();
		foreach ($constants as $x)
		{
			foreach ($constants as $y)
			{
				if (!isset($mapCurrency[$x]))
				{
					$mapCurrency[$x] = array();
				}

				if ($x == $y)
				{
					continue;
				}
				elseif (Currency::UAH == $x)
				{
					$mapCurrency[$x][$y] = 0;
					$yRate = $this->parseContentFrom($pageContent, $y);
					if (0 != $yRate)
					{
						$mapCurrency[$x][$y] = 1 / $yRate;
					}
				}
				elseif (Currency::UAH == $y)
				{
					$mapCurrency[$x][$y] = $this->parseContentFrom($pageContent, $x);
				}
				else
				{
					$mapCurrency[$x][$y] = 0;
					$yRate = $this->parseContentFrom($pageContent, $y);
					if (0 != $yRate)
					{
						$mapCurrency[$x][$y] = $this->parseContentFrom($pageContent, $x) / $yRate;
					}
				}
			}
		}

		return $mapCurrency;
	}

	/**
	 * Возвращает коэффициент умножения для конвертации валюты в UAH.
	 *
	 * @param string	$content			Контент страницы
	 * @param int		$fromCurrency		Код валюты
	 *
	 * @return float
	 */
	private function parseContentFrom($content, $fromCurrency)
	{
		$content = preg_replace("/\n/", ' ', $content);
		$regx = '/\<tr\>\s*\<td class="cell_c"\>' . $fromCurrency .  '\<\/td\>.*\<td class="cell_c"\>(\d+)\<\/td\>.*\<td class="cell_c"\>(.+)\<\/td\>\s*\<\/tr\>/U';
		preg_match($regx, $content, $matches);
		if (empty($matches))
		{
			return 0;
		}
		return $matches[2] / $matches[1];
	}

}
