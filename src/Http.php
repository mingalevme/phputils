<?php

namespace Mingalevme\Utils;

class Http
{
    /**
     * Parse raw http headers string or array of headers (i.e. $http_response_header)
     * 
     * @param array|string $input
     * @param int $statusCode
     * @param string $statusLine
     * @return array
     * @throws \InvalidArgumentException
     */
    public static function parseHeaders($input, &$statusCode=null, &$statusLine=null)
    {
        if (is_string($input)) {
            $headers = explode(\PHP_EOL, $input);
        } elseif (is_array($input)) {
            $headers = $input;
        } else {
            throw new \InvalidArgumentException();
        }
        
        $result = [];
        
        foreach ($headers as $header) {
            if (trim($header) === '') {
                continue;
            }
            
            $t = \explode(':', $header, 2);
            
            if (isset($t[1]) ) {
                $result[\trim($t[0])] = \trim($t[1]);
            } else { // response status line
                $statusLine = trim($header);
                if (preg_match("#HTTP/[0-9\.]+\s+([0-9]+)#", $header, $out)) {
                    $statusCode = \intval($out[1]);
                }
            }
        }
        
        return $result;
    }
}
