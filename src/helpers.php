<?php

use Mingalevme\Utils\Url;
use Mingalevme\Utils\Arr;
use Mingalevme\Utils\Json;

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
        
        $ctx = \stream_context_create(\array_replace_recursive([
            'http'=> [
                'timeout' => 30,
                'ignore_errors' => true,
            ],
        ], (array) $context));

        $exception = null;

        for ($i = 1; $i <= $attempts; $i++) {

            try {
                $responseBody = \file_get_contents($url, false, $ctx);
            } catch (\Throwable $exception) {
                continue;
            }
            
            $headers = \Mingalevme\Utils\Http::parseHeaders($http_response_header, $statusCode, $statusLine);
            
            if ($statusCode === 200) {
                return $responseBody;
            }
            
            if ($onError && ($customResponseBody = $onError($i, $statusCode, $responseBody)) !== null) {
                return $customResponseBody;
            }
            
        }

        if (isset($exception)) {
            throw $exception;
        }
        
        throw new \ErrorException("url_get_contents(...): failed to open stream: HTTP request failed! {$statusLine}");
    }
}

if (! function_exists('url_get_json_contents')) {
    
    function url_get_json_contents($url, array &$headers = null, array $context = null, $attempts = 1, $onError = null)
    {
        for ($i = 1; $i <= $attempts; $i++) {
            
            try {
                $data = url_get_contents($url, $headers, $context, 1, $onError);
            } catch (\ErrorException $e) {
                continue;
            }
            
            try {
                return jsond($data);
            } catch (\Mingalevme\Utils\Json\Exception\ParseException $e) {
                continue;
            }
            
        }
        
        throw $e;
    }
    
}

if (! function_exists('dom_get_elements_by_tag_name_and_class')) {
    /**
     * @param DOMDocument|DOMElement $parent
     * @param string $tagName
     * @param string $className
     * @return DOMElement[]
     */
    function dom_get_elements_by_tag_name_and_class($parent, string $tagName, string $className)
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

if (! function_exists('trytrytry')) {
    function trytrytry(callable $task, int $tries = 3, array $allowable = null) {

        if ($tries < 1) {
            throw new InvalidArgumentException('$tries must be greater than 0');
        }

        for ($i=0; $i<$tries; $i++) {
            try {
                return $task();
            } catch (\Throwable $e) {
                if ($allowable && !in_array(get_class($e), $allowable)) {
                    break;
                }
            }
        }

        throw $e;

    }
}
