<?php

namespace Mingalevme\Utils;

trait Contextable
{
    /**
     * @var array
     */
    protected $contextable;

    /**
     * @param array[] $extras
     * @return string
     */
    protected function getContext(...$extras)
    {
        return jsone($this->getRawContext(...$extras));
    }
    
    /**
     * @param array $extra
     * @return array
     */
    protected function getRawContext(...$extras)
    {
        if ($this->contextable != null) {
            $data = [];
            foreach ($this->contextable as $property) {
                $data[$property] = $this->{$property};
            }
        } else {
            $data = get_object_vars($this);
            unset($data['contextable']);
        }
        
        return array_compress(array_merge($data, ...$extras));
    }
}
