<?php

namespace Mingalevme\Utils;

use InvalidArgumentException;

class Sql
{
    /**
     * Convert PHP-array to SQL-array
     * https://stackoverflow.com/questions/5631387/php-array-to-postgres-array
     *
     * @param array $data
     * @param callable|string|callable-string $escape
     * @return string
     */
    public static function toArray(array $data, $escape = 'pg_escape_string')
    {
        $result = [];
        
        foreach ($data as $element) {
            if (is_array($element)) {
                $result[] = static::toArray($element, $escape);
            } elseif ($element === null) {
                $result[] = 'NULL';
            } elseif ($element === true) {
                $result[] = 'TRUE';
            } elseif ($element === false) {
                $result[] = 'FALSE';
            } elseif (is_numeric($element)) {
                $result[] =  $element;
            } elseif (is_string($element)) {
                $result[] = "'" . $escape($element) . "'";
            } else {
                throw new InvalidArgumentException("Unsupported array item");
            }
        }
        
        return sprintf('ARRAY[%s]', implode(',', $result));
    }
    
    public static function assemble(string $sql, array $bindings)
    {
        foreach ($bindings as $i => $binding) {
            if ($binding instanceof \DateTime) {
                $bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
            } else if (is_bool($binding)) {
                $bindings[$i] = $binding ? 'true' : 'false';
            } else if (is_string($binding)) {
                $bindings[$i] = "'$binding'";
            }
        }

        return vsprintf(str_replace(['%', '?'], ['%%', '%s'], $sql), $bindings); 
    }
}
