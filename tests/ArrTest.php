<?php

namespace Mingalevme\Tests\Utils;

use Mingalevme\Utils\Arr;

class ArrTest extends TestCase
{
    public function testHasObjectWithKeyAndValue()
    {
        $input = [
            [
                'foo1' => 'bar1',
                'bar1' => 'foo1',
            ],
            [
                'foo2' => 'bar2',
                'bar2' => 'foo2',
            ],
            [
                'foo3' => 'bar3',
                'bar3' => 'foo3',
            ],
        ];

        $this->assertTrue(Arr::hasObjectWithKeyAndValue($input, 'bar2', 'foo2'));
        $this->assertFalse(Arr::hasObjectWithKeyAndValue($input, 'bar4', 'foo4'));
    }
}
