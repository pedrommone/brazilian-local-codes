<?php

namespace Pedrommone\BrazilianLocalCodes;

class Proxy
{
    public static function codes(): array
    {
        return json_decode(file_get_contents(__DIR__ . '/../data/local-codes.json'));
    }
}
