<?php

namespace Forge\Property;

use Forge\ForgeParserMarker;

class IncludeProperty extends \Forge\ForgeProperty
{
    public function include($params)
    {
        $view = $this->parseParams($params);

        $parts = $this->forge()->loadView($view);

        $this->parser()->addParts($parts);
    }

    public function extends($params)
    {
        $this->include($params);
    }
}
