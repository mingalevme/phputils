<?php

namespace Mingalevme\Tests\Utils;

use Mingalevme\Utils\Arr;

class ArrTest extends TestCase
{
    /**
     * Just integration testing, for unit tests
     * @see https://github.com/mingalevme/phputils-arr/blob/master/tests/ArrTest.php
     */
    public function testUrlIntegration()
    {
        $this->assertSame('Mingalevme\Utils\Arr', Arr::class);
    }
}
