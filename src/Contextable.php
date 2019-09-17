<?php

namespace Mingalevme\Utils;

trait Contextable
{
    /**
     * @param array[] $extras
     * @return string
     */
    protected function getContext(...$extras)
    {
        return jsone($this->getRawContext(...$extras));
    }
    
    /**
     * @param array $extras
     * @return array
     */
    protected function getRawContext(...$extras)
    {
        if (property_exists($this, 'contextable')) {
            $data = [];
            foreach ($this->{'contextable'} as $property) {
                $data[$property] = $this->{$property};
            }
        } else {
            $data = get_object_vars($this);
            unset($data['contextable']);
        }
        
        return array_compress(array_merge($data, ...$extras));
    }
}
