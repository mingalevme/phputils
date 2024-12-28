<?php

namespace Mingalevme\Utils;

class DomainName
{
    public static function reverse($domain)
    {
        return \implode('.', \array_reverse(\explode('.', $domain)));
    }
}
