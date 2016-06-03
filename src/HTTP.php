<?php

namespace Mingalevme\Utils;

class HTTP
{
    const USER      = 'user';
    const SCHEME    = 'scheme';
    const HOST      = 'host';
    const QUERY     = 'query';
    const FRAGMENT  = 'fragment';
    
    /**
     * Build an URL
     * 
     * The parts of the second URL will be merged into the first according to the flags argument. 
     * @param string $url The URL to parse
     * @param array $parts [optional] Associative array like parse_url() returns
     * @param array &$new_url [optional] If set, it will be filled with the parts of the composed url like parse_url() would return
     * @return string The new URL string
     */
    public static function buildUrl($url, $parts=array(), &$new_url=false)
    {
        $url = trim($url);

        // Aliases
        $aliases = array(
            'u' => 'user',
            's' => 'scheme',
            'h' => 'host',
            'q' => 'query',
            'f' => 'fragment'
        );
        
        // Resolve aliases
        foreach ($parts as $k => $value) {
            if (isset($aliases[strtolower($k)])) {
                $key = $aliases[strtolower($k)];
                if ($k === strtoupper($k)) {
                    $parts[strtoupper($key)] = $value;
                } else {
                    $parts[$key] = $value;
                }
                unset($parts[$k]);
            }
        }

        // Parse the original URL
        $parse_url = parse_url($url);
        
        foreach ($parts as $key => $value) {
            if ($value === NULL) {
                unset($parse_url[$key]);
            } elseif ($key === strtoupper($key) || isset($parse_url[$key]) == FALSE) {
                $parse_url[strtolower($key)] = $parts[$key];
            } elseif ($key == 'path') {
                $parse_url['path'] = rtrim(str_replace(basename($parse_url['path']), '', $parse_url['path']), '/') . '/' . ltrim($parts['path'], '/');
            } elseif ($key == 'query') {
                $baseQuery = array();
                parse_str($parse_url['query'], $baseQuery);
                $query = array();
                parse_str($parts['query'], $query);
                $query = array_merge($baseQuery, $query);
                $parse_url['query'] = http_build_query($query);
            }
        }
        
        if (isset($parse_url['scheme']) == FALSE) {
            $parse_url['scheme'] = 'http';
        }

        $new_url = $parse_url;

        return
            ((isset($parse_url['scheme'])) ? $parse_url['scheme'] . '://' : '')
            .((isset($parse_url['user'])) ? $parse_url['user'] . ((isset($parse_url['pass'])) ? ':' . $parse_url['pass'] : '') .'@' : '')
            .((isset($parse_url['host'])) ? $parse_url['host'] : '')
            .((isset($parse_url['port'])) ? ':' . $parse_url['port'] : '')
            .((isset($parse_url['path'])) ? $parse_url['path'] : '')
            .((isset($parse_url['query'])) ? '?' . $parse_url['query'] : '')
            .((isset($parse_url['fragment'])) ? '#' . $parse_url['fragment'] : '')
            ;
    }
}
