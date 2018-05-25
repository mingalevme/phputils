<?php

namespace Mingalevme\Utils;

trait Contextable
{
    /**
     * @var array
     */
    protected $contextable;

    /**
     * @param array $extra
     * @return string
     */
    protected function getContext($extra = [])
    {
        return jsone($this->getRawContext($extra));
    }
    
    /**
     * @param array $extra
     * @return array
     */
    protected function getRawContext($extra = [])
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
        
        return array_compress(array_merge($data, $extra));
    }
}
