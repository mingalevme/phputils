<?php

declare(strict_types=1);

namespace Mingalevme\Utils;

use DOMDocument;
use DOMElement;

/**
 * @readonly
 */
class Dom
{
    /**
     * @param DOMDocument|DOMElement $parent
     * @param non-empty-string $tagName
     * @param non-empty-string $className
     * @return list<DOMElement>
     */
    public static function getElementsByTagNameAndClass($parent, string $tagName, string $className): array
    {
        $nodes = [];

        /** @var DOMElement $node */
        foreach ($parent->getElementsByTagName($tagName) as $node) {
            if (preg_match("/\b{$className}\b/", $node->getAttribute('class'))) {
                $nodes[] = $node;
            }
        }

        return $nodes;
    }
}