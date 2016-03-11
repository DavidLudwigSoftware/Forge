<?php

namespace Forge;


class ForgeToken
{
    const EOF = 0;
    const HTML = 1;
    const PRINT_STATEMENT = 2;
    const PROPERTY = 3;
    const PARAMETERS = 4;

    private $_type;
    private $_content;

    public function __construct(int $type, $content = Null)
    {
        $this->_type = $type;
        $this->_content = $content;
    }

    public function type()
    {
        return $this->_type;
    }

    public function content()
    {
        return $this->_content;
    }
}
