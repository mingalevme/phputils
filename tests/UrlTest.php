<?php

declare(strict_types=1);

namespace Mingalevme\Tests\Utils;

use Mingalevme\Utils\Url;

final class UrlTest extends TestCase
{
    /**
     * Just integration testing, for unit tests
     * @see https://github.com/mingalevme/phputils-url/blob/master/tests/UrlTest.php
     */
    public function testUrlIntegration(): void
    {
        $this->assertSame('Mingalevme\Utils\Url', Url::class);
    }
}
