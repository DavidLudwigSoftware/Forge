<?php

namespace Forge;


class ForgePrintStatement
{
    const SYMBOL = Null;

    private $_symbols;
    private $_content;

    public function __construct($symbols, $content)
    {
        $this->_symbols = $symbols;
        $this->_content = $content;
    }

    public function symbols()
    {
        return $this->_symbols;
    }

    public function content()
    {
        return $this->_content;
    }
}
