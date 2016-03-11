<?php

namespace Forge;


class ForgeParserMarker
{
    private $_key;
    private $_name;
    private $_content;

    public function __construct($key, $name, $content = Null)
    {
        $this->_key = $key;
        $this->_name = $name;
        $this->_content = $content ?: '';
    }

    public function key()
    {
        return $this->_key;
    }

    public function name()
    {
        return $this->_name;
    }

    public function fill($content)
    {
        $this->_content = $content;
    }

    public function __toString()
    {
        if (gettype($this->_content) == 'array')

            return implode('', $this->_content);
            
        return $this->_content;
    }
}
