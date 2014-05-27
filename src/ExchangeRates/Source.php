<?php
/**
 * @category		SolDeveloper
 * @package		ExchangeRates
 * @author		Sol Developer <sol.developer@gmail.com>
 * @copyright		Copyright (c) 2014 Sol Developer (https://github.com/soldeveloper/exchange-rates)
 * @license		http://www.gnu.org/copyleft/lesser.html
 */

namespace ExchangeRates;

/**
 * Общий предок всех источников данных.
 */
abstract class Source
{

	/**
	 * Метод GET запроса данных по HTTP.
	 */
	const HTTP_METH_GET = 1;

	/**
	 * Метод POST запроса данных по HTTP.
	 */
	const HTTP_METH_POST = 3;

	/**
	 * Ключ для хранения хешмап таблицы конвертации валют в APC кеше.
	 */
	const APC_MAP_CURRENCY_KEY = 'MAP-CURRENCY-HASH-TABLE';

	/**
	 * Хешмап таблица конвертации валют.
	 *
	 * @var null|array
	 */
	private $mapCurrency = null;

	/**
	 * Конвертирует указанную сумму в исходной валюте в конечную и возвращает значение.
	 *
	 * @param float	$sum				Сумма
	 * @param int		$fromCurrency		Исходная валюта
	 * @param int		$toCurrency		Конечная валюта
	 *
	 * @return float
	 */
	public function convert($sum, $fromCurrency, $toCurrency)
	{
		if ($fromCurrency == $toCurrency)
		{
			return $sum;
		}

		return $sum * $this->getMapCurrency()[$fromCurrency][$toCurrency];
	}

	/**
	 * Создает и возвращает хешмап таблицу где первое измерение это
	 * исходная валюта, второе конечная валюта, значение в ячейке - коэффициент умножения.
	 * Возвращает текущий объект.
	 *
	 * @return array
	 *
	 * @throws Exception
	 */
	abstract protected function createMapCurrency();

	/**
	 * Производит HTTP запрос внешнего ресурса для получения данных необходимых
	 * для создания хешмап таблицы конвертации валют.
	 * Возвращает ответ запроса с дополнительными данными об ошибках.
	 *
	 * @param string	$url			URL запроса
	 * @param array	$params		Параметры запроса
	 * @param int		$method		Метод запроса
	 *
	 * @return array
	 */
	protected function requestHTTPClient($url, $params = array(), $method = self::HTTP_METH_POST)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Expect:'));
		curl_setopt($curl, CURLOPT_TIMEOUT, 60);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$query = http_build_query($params);
		switch ($method)
		{
			case self::HTTP_METH_POST:
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
				break;
			default:
				curl_setopt($curl, CURLOPT_POST, false);
				$url .= '?' . $query;
				break;
		}
		curl_setopt($curl, CURLOPT_URL, $url);
		$content = curl_exec($curl);
		$errno = curl_errno($curl);
		$error = curl_error($curl);
		curl_close($curl);
		return array(
			'content' => $content,
			'errno' => $errno,
			'error' => $error
		);
	}

	/**
	 * Возвращает хешмап таблицу.
	 *
	 * @return array|null
	 */
	public function getMapCurrency()
	{
		if (
			is_null($this->mapCurrency) &&
			function_exists('apc_cache_info') &&
			apc_exists(self::APC_MAP_CURRENCY_KEY)
		)
		{
			/**
			 * Если в APC кеше есть хешмап таблица то она будет использована.
			 */
			$this->mapCurrency = apc_fetch(self::APC_MAP_CURRENCY_KEY);
		}
		if (is_null($this->mapCurrency))
		{
			/**
			 * Создает новую хешмап таблицу для конвертации валют.
			 */
			$this->mapCurrency = $this->createMapCurrency();
			if (function_exists('apc_cache_info'))
			{
				apc_store(self::APC_MAP_CURRENCY_KEY, $this->mapCurrency, 43200);		// 12 hours
			}
		}
		return $this->mapCurrency;
	}

}
