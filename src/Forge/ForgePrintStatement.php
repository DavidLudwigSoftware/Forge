<?php

namespace Forge;


class ForgePrintStatement
{
    /**
     * The start and end symbols of a print statement
     * @var string
     */
    private $_symbols;

    /**
     * Store the content of the print statement
     * @var string
     */
    private $_content;


    /**
     * Create a ForgePrint statement
     * @param string $symbols The symbols that start and end the print statement
     * @param string $content The content of the print statement
     */
    public function __construct(string $symbols, string $content)
    {
        $this->_symbols = $symbols;
        $this->_content = trim($content);
    }

    /**
     * The symbols that start and end the print statement
     * @return string
     */
    public function symbols()
    {
        return $this->_symbols;
    }

    /**
     * The content of the print statement
     * @return string
     */
    public function content()
    {
        return $this->_content;
    }
}
