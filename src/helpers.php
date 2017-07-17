<?php

use Mingalevme\Utils\Url;
use Mingalevme\Utils\Arr;
use Mingalevme\Utils\Json;

if (! function_exists('array_compress')) {
    /**
     * Recursivly remove all falsy values from array
     *
     * @param  array $array
     * @return array
     */
    function array_compress(array $array)
    {
        return Arr::compress($array);
    }
}

if (! function_exists('build_url')) {
    /**
     * \Mingalevme\Utils\Url::build function alias
     *
     * @param  string $json
     * @return mixed
     */
    function build_url($url, $parts=[], &$new_url = false)
    {
        return Url::build($url, $parts, $new_url);
    }
}

if (! function_exists('jsone')) {
    /**
     * \Mingalevme\Utils\Json::encode function alias
     *
     * @param  mixed $data
     * @return string
     */
    function jsone($data)
    {
        return Json::e($data);
    }
}

if (! function_exists('jsond')) {
    /**
     * \Mingalevme\Utils\Json::decode function alias
     *
     * @param  string $json
     * @return mixed
     */
    function jsond($json)
    {
        return Json::d($json);
    }
}

if (! function_exists('url_get_contents')) {
    
    /**
     * Reads remote content into a string
     * 
     * @param string $url
     * @param array &$headers
     * @param array $context
     * @param int $attempts
     * @param callable $onError
     * @return string
     * @throws \ErrorException
     */
    function url_get_contents($url, array &$headers = null, array $context = null, $attempts = 1, $onError = null)
    {
        if (Url::isAbsolute($url) === false) {
            throw new \InvalidArgumentException("Invalid value for \$url");
        }
        
        if ($attempts < 1) {
            throw new \InvalidArgumentException("Invalid value for \$attempts");
        }
        
        $ctx = \stream_context_create(\array_merge_recursive([
            'http'=> [
                'timeout' => 30,
                'ignore_errors' => true,
            ],
        ], (array) $context));

        for ($i = 1; $i <= $attempts; $i++) {
            
            $responseBody = \file_get_contents($url, false, $ctx);
            
            $headers = \Mingalevme\Utils\Http::parseHeaders($http_response_header, $statusCode, $statusLine);
            
            if ($statusCode === 200) {
                return $responseBody;
            }
            
            if ($onError && ($customResponseBody = $onError($i, $statusCode, $responseBody)) !== null) {
                return $customResponseBody;
            }
            
        }
        
        throw new \ErrorException("url_get_contents(...): failed to open stream: HTTP request failed! {$statusLine}");
    }
}
