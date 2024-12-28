<?php

declare(strict_types=1);

namespace Mingalevme\Tests\Utils;

use Mingalevme\Utils\Sql;

final class SqlTest extends TestCase
{
    public function testToArray(): void
    {
        $escaper = function ($str) {
            return addslashes($str);
        };
        $this->assertSame("ARRAY['foo','bar']", Sql::toArray(['foo', 'bar'], $escaper));
        $this->assertSame("ARRAY[1,2]", Sql::toArray([1, 2]));
        $this->assertSame("ARRAY[1,2]", Sql::toArray(['1', '2']));
        $this->assertSame("ARRAY['foo\\\"bar','bar\'foo']", Sql::toArray(['foo"bar', 'bar\'foo'], $escaper));
        $this->assertSame("ARRAY[ARRAY['foo\\\"bar'],ARRAY['bar\'foo']]", Sql::toArray([['foo"bar'], ['bar\'foo']], $escaper));
    }
}
