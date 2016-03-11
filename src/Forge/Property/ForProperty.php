<?php

namespace Forge\Property;

use Forge\ForgeToken as Token;

class ForProperty extends \Forge\ForgeProperty
{
    public function for($params)
    {
        return "<?php for ($params): ?>";
    }

    public function endfor()
    {
        return "<?php endfor; ?>";
    }
}
