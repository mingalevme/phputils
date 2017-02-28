<?php

namespace Mingalevme\Utils;

use Mingalevme\Utils\Arr;
use Mingalevme\Utils\Json;

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
        return Json::e($this->getRawContext($extra));
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
            unset($data['contextable']);
        }
        
        return Arr::compact(array_merge($data, $extra));
    }
}
