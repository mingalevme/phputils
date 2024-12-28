<?php

declare(strict_types=1);

namespace Mingalevme\Tests\Utils;

use Mingalevme\Utils\Number;

final class NumberTest extends TestCase
{
    public function testIsOddNumber(): void
    {
        $this->assertSame(false, Number::isOddNumber(-2));
        $this->assertSame(true, Number::isOddNumber(-1));
        $this->assertSame(false, Number::isOddNumber(0));
        $this->assertSame(true, Number::isOddNumber(1));
        $this->assertSame(false, Number::isOddNumber(2));
    }

    public function testIsEvenNumber(): void
    {
        $this->assertSame(true, Number::isEvenNumber(-2));
        $this->assertSame(false, Number::isEvenNumber(-1));
        $this->assertSame(true, Number::isEvenNumber(0));
        $this->assertSame(false, Number::isEvenNumber(1));
        $this->assertSame(true, Number::isEvenNumber(2));
    }
}