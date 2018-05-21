<?php


namespace Mingalevme\Tests\Utils;


use Mingalevme\Utils\Url;

class UrlTest extends TestCase
{
    /**
     * Just integration testing, for unit tests
     * @see https://github.com/mingalevme/phputils-url/blob/master/tests/UrlTest.php
     */
    public function testUrlIntegration()
    {
        $this->assertSame('Mingalevme\Utils\Url', Url::class);
    }
}
