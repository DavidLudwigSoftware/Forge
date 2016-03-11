<?php

namespace Forge;


class ForgeProperty
{
    /**
     * Store the current Forge parser
     * @var Forge\ForgeParser
     */
    private $_parser;

    /**
     * Create the instance of the property
     * @param Forge\ForgeParser $parser The current Forge parser
     */
    public function __construct(ForgeParser $parser)
    {
        $this->_parser = $parser;

        $this->init();
    }

    /**
     * Initializer function called when the property is being processed
     * @return void
     */
    public function init()
    {

    }

    /**
     * Get the Forge instance
     * @return [type] [description]
     */
    public function forge()
    {
        return $this->_parser->forge();
    }

    /**
     * Get the Forge parser
     * @return Forge\ForgeParser
     */
    public function parser()
    {
        return $this->_parser;
    }

    /**
     * Get the Forge parser's storage
     * @return Forge\ForgeParserStorage
     */
    public function storage()
    {
        return $this->_parser->storage();
    }

    /**
     * Evaluate received parameters
     * @param  string $params The parameters received
     * @return mixed          The result of the parameter evaluation
     */
    public function parseParams(string $params)
    {
        return eval("return $params;");
    }
}
