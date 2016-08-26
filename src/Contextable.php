<?php

namespace Mingalevme\Utils;

use Mingalevme\Utils\Arr;
use Mingalevme\Utils\JSON;

trait Contextable
{
    /**
     * @var array
     */
    protected $contextable;

    /**
     * @return string
     */
    protected function getContext(array $extra = [])
    {
        return JSON::e($this->getRawContext($extra));
    }
    
    /**
     * @return array
     */
    protected function getRawContext(array $extra = [])
    {
        if ($this->contextable) {
            $data = [];
            foreach ($this->contextable as $property) {
                $data[$property] = $this->{$property};
            }
        } else {
            $data = get_object_vars($this);
        }
        
        return Arr::compact(array_merge($extra, $data));
    }
}
