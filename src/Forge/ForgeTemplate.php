<?php

namespace Forge;


class ForgeTemplate
{
    public function __construct(string $content = Null)
    {
        $this->_content = $content ?: '';
    }

    public function render($variables = [])
    {
        extract($variables);
        
        ob_start();

        eval("?>$this->_content<?php");

        $html = ob_get_clean();

        return $html;
    }
}
