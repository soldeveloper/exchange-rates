<?php

/**
 * @category		SolDeveloper
 * @package		ExchangeRates
 * @author		Sol Developer <sol.developer@gmail.com>
 * @copyright		Copyright (c) 2014 Sol Developer (https://github.com/soldeveloper/exchange-rates)
 * @license		http://www.gnu.org/copyleft/lesser.html
 */

namespace ExchangeRates;

use ExchangeRates\Source;

/**
 * Класс для конвертации валют.
 */
class Conversion
{

	/**
	 * Источник данных для конвертации.
	 *
	 * @var Source
	 */
	private $source = null;

	/**
	 * Устанавливает входные значения.
	 *
	 * @param Source		$source		Источник данных
	 */
	private function __construct($source)
	{
		$this->setSource($source);
	}

	/**
	 * Создает и возвращает API для конвертации валют.
	 * В случае ошибки бросает исключение.
	 *
	 * @param string		$alias		Алиас источника данных
	 *
	 * @return Conversion
	 *
	 * @throws Exception
	 */
	public static function factory($alias)
	{
		$className = '\ExchangeRates\Source\\' . str_replace(' ', '', ucwords(str_replace('.', ' ', strtolower($alias))));
		if (!class_exists($className))
		{
			throw new Exception('Not found source.');
		}
		return new self(new $className());
	}

	/**
	 * Конвертирует указанную сумму в исходной валюте в конечную с
	 * использованием источника данных для конвертации и возвращает значение.
	 *
	 * @param float	$sum				Сумма
	 * @param int		$fromCurrency		Исходная валюта
	 * @param int		$toCurrency		Конечная валюта
	 *
	 * @return float
	 */
	public function convert($sum, $fromCurrency, $toCurrency)
	{
		return $this
			->getSource()
			->convert($sum, $fromCurrency, $toCurrency);
	}

	/**
	 * Возвращает Javascript для конвертации сумм в исходной валюте в конечную с
	 * использованием источника данных для конвертации и возвращает значение.
	 *
	 * @return string
	 */
	public function getJavaScriptAPI()
	{
		$currencyReflectionClas = new \ReflectionClass('\ExchangeRates\Currency');
		$constants = $currencyReflectionClas->getConstants();
		$mapCurrency = $this
			->getSource()
			->getMapCurrency();
		$javaScript = '
<script type="text/javascript">
	ExchangeRates = {
		Currency : ' . json_encode($constants) . ',
		MapCurrency : ' . json_encode($mapCurrency) . ',
		Convert : function (sum, fromCurrency, toCurrency) {
			if (fromCurrency == toCurrency) {
				return sum;
			}
			return sum * this.MapCurrency[fromCurrency][toCurrency];
		}
	};
</script>
';
		return $javaScript;
	}

	/**
	 * Возвращает источник данных.
	 *
	 * @return Source
	 */
	private function getSource()
	{
		return $this->source;
	}

	/**
	 * Устанавливает источник данных.
	 * Возвращает текущий объект.
	 *
	 * @param Source	$source		Источник данных
	 *
	 * @return self
	 */
	private function setSource(Source $source)
	{
		$this->source = $source;
		return $this;
	}

}
