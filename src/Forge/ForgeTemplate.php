<?php

namespace Forge;


class ForgeTemplate
{
    public function __construct(string $content = Null)
    {
        $this->_content = $content ?: '';
    }

    /**
     * Render a template into Html
     * @param  array $variables A list of variables to use in the template
     * @return string           The compiled view in Html form
     */
    public function render(array $variables = [])
    {
        extract($variables);

        ob_start();

        eval("?>$this->_content<?php");

        return ob_get_clean();
    }
}
