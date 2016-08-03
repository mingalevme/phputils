<?php

namespace Mingalevme\Utils;

class XML extends \SimpleXMLElement
{
    /**
     * Shortcut for new \SimpleXMLElement($data, $options, <b>false</b>, $ns, $is_prefix)
     * 
     * @param string $data
     * @param int $options
     * @param string $ns
     * @param bool $is_prefix
     * @return \static
     */
    public static function fromXml(string $data, int $options = 0, string $ns = "", bool $is_prefix = false)
    {
        return new static($data, $options, false, $ns, $is_prefix);
    }
    
    /**
     * Shortcut for new \SimpleXMLElement($data, $options, <b>true</b>, $ns, $is_prefix)
     * 
     * @param string $url
     * @param int $options
     * @param string $ns
     * @param bool $is_prefix
     * @return \static
     */
    public static function fromUrl(string $url, int $options = 0, string $ns = "", bool $is_prefix = false)
    {
        return new static($url, $options, true, $ns, $is_prefix);
    }

    public function addChild($name, $value = null, $namespace = null, $CDATA = false)
    {
        if ($value && $CDATA) {
            $sxe = parent::addChild($name, null, $namespace);
            $node = dom_import_simplexml($sxe);
            $node->appendChild($node->ownerDocument->createCDATASection($value));
        } else {
            return parent::addChild($name, $value ? htmlspecialchars($value) : $value, $namespace);
        }
    }

    public function addAttribute($name, $value = null, $namespace = null)
    {
        return parent::addAttribute($name, $value ? htmlspecialchars($value) : $value, $namespace);
    }
    
    /**
     * Safely get element attribute value
     * 
     * @param string $attr Attribute name
     * @param string $default Default value if element or attribute doesn't exist
     * @return string
     */
    public function getAttr(string $attr, string $default=null)
    {
        if (isset($this) === false) {
            return $default;
        }
        
        if (isset($this->attributes()->{$attr}) === false) {
            return $default;
        }

        return trim((string) $this->attributes()->{$attr});
    }
    
    /**
     * Get element value as text
     * 
     * @return string|null
     */
    public function asText()
    {
       if (isset($this)) {
           return trim($this);
       } else {
           return null;
       }
    }
    
    /**
     * Remove all unnecessary symbols
     * 
     * @return string
     */
    public function asCompressedXML()
    {
        return preg_replace('/>[\n\s\r]+?</', '><', $this->asXML());
    }
    
    /**
     * Get element child as string
     *
     * @param \SimpleXMLElement $el
     * @return string
     */
    public function getValue(string $child = null)
    {
        if (isset($this)) {
            if ($child && isset($this->{$child})) {
                return trim($this->{$child});
            } else {
                return trim($this);
            }
        }

        return null;
    }
}
