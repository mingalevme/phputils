<?php

declare(strict_types=1);

namespace Mingalevme\Tests\Utils;

use Mingalevme\Utils\Arr;

final class ArrTest extends TestCase
{
    /**
     * Just integration testing, for unit tests
     * @see https://github.com/mingalevme/phputils-arr/blob/master/tests/ArrTest.php
     */
    public function testUrlIntegration(): void
    {
        $this->assertSame('Mingalevme\Utils\Arr', Arr::class);
    }
}
