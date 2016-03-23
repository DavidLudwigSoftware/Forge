<?php

namespace Forge;


class ForgeParserMarker
{
    /**
     * The key of the marker
     * @var string
     */
    private $_key;

    /**
     * The name of the marker
     * @var string
     */
    private $_name;

    /**
     * The content of the marker
     * @var mixed
     */
    private $_content;


    /**
     * Create a marker to add data into later
     * @param string $key     The key of the marker
     * @param string $name    The name of the marker
     * @param mixed  $content The content of the marker
     */
    public function __construct(string $key, string $name, $content = Null)
    {
        $this->_key = $key;
        $this->_name = $name;
        $this->_content = $content ?: '';
    }

    /**
     * Get the key of the marker
     * @return string
     */
    public function key()
    {
        return $this->_key;
    }

    /**
     * Get the name of the marker
     * @return string name
     */
    public function name()
    {
        return $this->_name;
    }

    /**
     * Get the content of the marker
     * @return string
     */
    public function content()
    {
        return $this->_content;
    }

    /**
     * Fill the marker with content
     * @param  mixed $content
     * @return void
     */
    public function fill($content)
    {
        $this->_content = $content;
    }

    /**
     * Convert the marker to a string
     * @return string the marker's content
     */
    public function __toString()
    {
        if (gettype($this->_content) == 'array')

            return implode('', $this->_content);

        return (string) $this->_content;
    }
}
