<?php

namespace BrazilianLocalCodes;

class Proxy
{
    public static function codes(): \stdClass
    {
        return json_decode(__DIR__ . '/../data/local-codes.json');
    }
}
