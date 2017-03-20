<?php

namespace Mingalevme\Utils;

class Domain
{
    public static function reverse($domain)
    {
        return \implode('.', \array_reverse(\explode('.', $domain)));
    }
}
