<?php

namespace Forge;


class ForgeParserStorage implements \ArrayAccess
{
    private $_data;

    public function __construct()
    {
        $this->_data = array();
    }

    public function store($key, $value)
    {
        $this->_data[$key] = $value;
    }

    public function get($key)
    {
        return $this->offsetGet($key);
    }

    public function remove($key)
    {
        $this->offsetUnset($key);
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset))
        {
            $this->_data[] = $value;
        }
        else
        {
            $this->_data[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->_data[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->_data[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->_data[$offset]) ? $this->_data[$offset] : Null;
    }
}
