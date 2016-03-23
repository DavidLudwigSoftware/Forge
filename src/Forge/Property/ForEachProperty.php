<?php

namespace Forge\Property;

use Forge\ForgeToken as Token;

class ForEachProperty extends \Forge\ForgeProperty
{
    public function foreach($params)
    {
        return "<?php foreach ($params): ?>";
    }

    public function endforeach()
    {
        return "<?php endforeach; ?>";
    }
}
