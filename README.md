ExchangeRates
======

ExchangeRates module for currency conversion.

Installation
------------

#### Installing version 0.1 via Composer

* Get [Composer](http://getcomposer.org/)
* Create file composer.json if absent:

```json
{
	"repositories": [
		{
			"type": "package",
			"package": {
				"name": "soldeveloper/exchange-rates",
				"version": "0.1",
				"source": {
					"type": "git",
						"url": "https://github.com/soldeveloper/exchange-rates.git",
						"reference": "0.1"
				},
				"autoload": {
					"psr-0": {
						"ExchangeRates\\": "src/"
					}
				}
			}
		}
	],
  	"require":{
		"php": ">=5.4",
		"soldeveloper/exchange-rates": "0.1"
	}
}
```

* Run `composer update`.

#### Install latest version from source code repository

`git clone https://github.com/soldeveloper/exchange-rates.git`

### Requirements

- **PHP** >= 5.4

### Usage

* See [/example/sample.php](https://github.com/soldeveloper/exchange-rates/blob/master/example/example.php).
