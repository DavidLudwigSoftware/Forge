<?php

namespace Forge;


class ForgeParserStorage
{
    /**
     * The storage variable
     * @var array
     */
    private $_data;

    /**
     * Create a ForgeParserStorage instance
     */
    public function __construct()
    {
        $this->_data = array();
    }

    /**
     * Store some data in the storage
     * @param  mixed $key   The key to store the value under
     * @param  mixed $value The value to store
     * @return void
     */
    public function store($key, $value)
    {
        $this->_data[$key] = $value;
    }

    /**
     * Get the value from store if it exists
     * @param  mixed $key The key the value is stored under
     * @return mixed      The value stored under the key
     */
    public function get($key, $default = Null)
    {
        return isset($this->_data[$key]) ? $this->_data[$key] : $default;
    }

    /**
     * Remove data from the storage
     * @param  mixed $key The key to remove
     * @return void
     */
    public function remove($key)
    {
        unset($this->_data[$key]);
    }
}
