# Brazilian Local Codes
A full indexed local codes by city and state

# How it works

There is a [public file](https://www.anatel.gov.br/legislacao/resolucoes/16-2001/383-resolucao-263), that holds all available Brazilian local codes. We crawled it and transformed it as json. 

The [json file](data/local-codes.json) holds all brazilian local codes using the following object:

```json
[
  {
    "city": "ACREL\u00c2NDIA",
    "code": "68",
    "state": "AC"
  },
  ...
]
```

### Using PHP

Require it `composer require pedrommone/brazilian-local-coodes` then use the proxy interface `\BrazilianLocalCodes\Proxy::codes()`

# Updating

I don't think it will change in any future, but I'll keep the crawler engine here.

Can you run by yourself? Simple run `docker-compose run app ./app crawler`
