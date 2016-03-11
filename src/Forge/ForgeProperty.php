<?php

namespace Forge;


class ForgeProperty
{
    private $_parser;

    public function __construct(ForgeParser $parser)
    {
        $this->_parser = $parser;

        $this->init();
    }

    public function init()
    {

    }

    public function forge()
    {
        return $this->_parser->forge();
    }

    public function parser()
    {
        return $this->_parser;
    }

    public function storage()
    {
        return $this->_parser->storage();
    }

    public function parseParams($params)
    {
        return eval("return $params;");
    }
}
