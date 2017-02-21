<?php

use Mingalevme\Utils\Arr;

if (! function_exists('array_compress')) {
    /**
     * Recursivly remove all falsy values from array
     *
     * @param  array $array
     * @return array
     */
    function array_compact(array $array)
    {
        return Arr::compress($array);
    }
}
