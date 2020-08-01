<?php

use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testObjectValues($city, $code, $state)
    {
        $this->assertIsString($city);
        $this->assertIsInt($code);
        $this->assertGreaterThan(0, $code);
        $this->assertMatchesRegularExpression('/\w{2}/', $state);
    }

    public function dataProvider()
    {
        return json_decode(file_get_contents(__DIR__ . '/../data/local-codes.json'), true);
    }
}
