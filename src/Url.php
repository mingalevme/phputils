<?php

namespace Mingalevme\Utils;

class Url
{
    const SCHEME    = 'scheme';
    const HOST      = 'host';
    const PORT      = 'port';
    const USER      = 'user';
    const PASS      = 'pass';
    const PATH      = 'path';
    const QUERY     = 'query';
    const FRAGMENT  = 'fragment';
    
    /*static protected $mapping = [
        \PHP_URL_SCHEME => 'scheme',
        \PHP_URL_HOST => 'host',
        \PHP_URL_PORT => 'port',
        \PHP_URL_USER => 'user',
        \PHP_URL_PASS => 'pass',
        \PHP_URL_PATH => 'path',
        \PHP_URL_QUERY => 'query',
        \PHP_URL_FRAGMENT => 'fragment',
    ];*/
    
    static protected $mapping = [
        self::SCHEME => \PHP_URL_SCHEME,
        self::HOST => \PHP_URL_HOST,
        self::PORT => \PHP_URL_PORT,
        self::USER => \PHP_URL_USER,
        self::PASS => \PHP_URL_PASS,
        self::PATH => \PHP_URL_PATH,
        self::QUERY => \PHP_URL_QUERY,
        self::FRAGMENT => \PHP_URL_FRAGMENT,
    ];

    /**
     * Build an URL
     * 
     * The parts of the second URL will be merged into the first according to the flags argument. 
     * @param string $url The URL to parse
     * @param array $parts [optional] Associative array like parse_url() returns
     * @param array &$new_url [optional] If set, it will be filled with the parts of the composed url like parse_url() would return
     * @return string The new URL string
     */
    public static function build($url, $parts=[], &$new_url=false)
    {
        $aliases = [
            'u' => 'user',
            's' => 'scheme',
            'h' => 'host',
            'p' => 'path', // PATH (!) NOT PASSWORD BECAUSE PATH IS MORE USED
            'q' => 'query',
            'f' => 'fragment',
        ];
        
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
        $parse_url = parse_url(trim($url));
        
        foreach ($parts as $key => $value) {
            if ($value === NULL) {
                unset($parse_url[$key]);
            } elseif ($key === strtoupper($key) || isset($parse_url[$key]) == FALSE) {
                $parse_url[strtolower($key)] = $parts[$key];
            } elseif ($key == 'path') {
                $parse_url['path'] = rtrim(str_replace(basename($parse_url['path']), '', $parse_url['path']), '/') . '/' . ltrim($parts['path'], '/');
            } elseif ($key == 'query') {
                
                $baseQuery = [];
                parse_str($parse_url['query'], $baseQuery);
                
                if (is_array($value)) {
                    $query = $value;
                } else {
                    $query = array();
                    parse_str($value, $query);
                }
                
                $parse_url['query'] = array_merge($baseQuery, $query);
            }
        }
        
        if (isset($parse_url['query']) && is_array($parse_url['query'])) {
            $parse_url['query'] = static::buildQueryString($parse_url['query']);
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
    
    /**
     * Almost the same as parse_url, except $component is expected local consts
     * 
     * @param string $url The URL to parse. Invalid characters are replaced by _.
     * @param int $component Optional. Specify one of
     * self::SCHEME,
     * self::HOST,
     * self::PORT,
     * self::USER,
     * self::PASSWORD,
     * self::PATH,
     * self::QUERY,
     * self::FRAGMENT
     * to retrieve just a specific URL component as a string (except
     * when self::PORT is given, in which case the return value will be an integer).
     * @throws \Exception If $component is unknown.
     * @return array|string|int|bool If the component parameter is omitted,
     * an associative array is returned. At least one element will be present
     * within the array. If the component parameter is specified, self::parse()
     * returns a string (or an integer, in the case of self::PORT) instead of
     * an array. If the requested component doesn't exist within the given URL,
     * NULL will be returned.
     * 
     */
    public static function parse($url, string $component = null)
    {
        if ($component && isset(self::$mapping[$component]) === false) {
            throw new \Exception("Unknown component: {$component}");
        }
        
        return \parse_url($url, $component ? self::$mapping[$component] : -1);
    }
    
    /**
     * (PHP 5)<br/>
     * Generate URL-encoded query string
     * @link http://php.net/manual/en/function.http-build-query.php
     * @param mixed $query_data <p>
     * May be an array or object containing properties.
     * </p>
     * <p>
     * If <i>query_data</i> is an array, it may be a simple
     * one-dimensional structure, or an array of arrays (which in
     * turn may contain other arrays).
     * </p>
     * <p>
     * If <i>query_data</i> is an object, then only public
     * properties will be incorporated into the result.
     * </p>
     * @param string $numeric_prefix [optional] <p>
     * If numeric indices are used in the base array and this parameter is
     * provided, it will be prepended to the numeric index for elements in
     * the base array only.
     * </p>
     * <p>
     * This is meant to allow for legal variable names when the data is
     * decoded by PHP or another CGI application later on.
     * </p>
     * @param string $arg_separator [optional] <p>
     * arg_separator.output
     * is used to separate arguments, unless this parameter is specified,
     * and is then used.
     * </p>
     * @param int $enc_type [optional] <p>
     * By default, <b>PHP_QUERY_RFC1738</b>.
     * </p>
     * <p>
     * If <i>enc_type</i> is
     * <b>PHP_QUERY_RFC1738</b>, then encoding is performed per
     * RFC 1738 and the
     * application/x-www-form-urlencoded media type, which
     * implies that spaces are encoded as plus (+) signs.
     * </p>
     * <p>
     * If <i>enc_type</i> is
     * <b>PHP_QUERY_RFC3986</b>, then encoding is performed
     * according to RFC 3986, and
     * spaces will be percent encoded (%20).
     * </p>
     * @return string a URL-encoded string.
     */
    public static function buildQueryString(array $params, $prefix = null, $separator = '&', $enctype = null)
    {
        if ($enctype) {
            return http_build_query($params, $prefix, $separator, $enctype);
        } else {
            return http_build_query($params, $prefix, $separator);
        }
    }

        /**
     * Set (if not specified) scheme and host to $url based on $baseUrl
     * 
     * @param string $url
     * @param string $baseUrl
     * @return type Description
     */
    public static function absolutizeUrl(string $url, string $baseUrl)
    {
        $components = parse_url($baseUrl);
        
        if (isset($components[self::SCHEME]) === false || isset($components[self::HOST]) === false) {
            throw new Exception("Malformed baseUrl: {$baseUrl}");
        }
        
        return static::build($url, [
            's' => $components[self::SCHEME],
            'h' => $components[self::HOST],
        ]);
    }
    
    /**
     * Check if url is absolute (based on scheme and host components)
     * 
     * @param string $url
     * @return bool
     */
    public static function isAbsolute($url)
    {
        $components = self::parse($url);
        
        return isset($components[self::SCHEME]) && isset($components[self::HOST]);
    }
    
    /**
     * Check if url is related (based on host component)
     * 
     * @param string $url
     * @return bool
     */
    public static function isRelated($url)
    {
        return !self::isAbsolute($url);
    }
    
    public static function isLocal($url)
    {
        $components = self::parse($url);
        
        if (isset($components[self::SCHEME]) === false || $components[self::SCHEME] === 'file') {
            return true;
        }
        
        return false;
    }
}
