<?php

namespace Mingalevme\Utils;

class Str extends \Illuminate\Support\Arr
{
    public static function random($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        $str = '';
        
        $max = \mb_strlen($keyspace, '8bit') - 1;
        
        for ($i = 0; $i < $length; ++$i) {
            $str .= $keyspace[\rand(0, $max)];
        }
        
        return $str;
    }
}
