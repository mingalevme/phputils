<?php

declare(strict_types=1);

namespace Mingalevme\Tests\Utils;

use Mingalevme\Utils\Xml;

class XmlTest extends TestCase
{
    public function test(): void
    {
        $xml = Xml::fromXml("<root><element attr='attr-value'>node-value</element></root>");
        self::assertSame($xml->getValue('element'), 'node-value');
        /** @var Xml $elementXml */
        $elementXml = $xml->element;
        self::assertSame($elementXml->getAttr('attr'), 'attr-value');
        self::assertSame($elementXml->getValue(), 'node-value');
    }
}
