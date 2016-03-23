<?php

namespace Forge\Property;

use Forge\ForgeToken as Token;

class WhileProperty extends \Forge\ForgeProperty
{
    public function while($params)
    {
        return "<?php while ($params): ?>";
    }

    public function endwhile()
    {
        return "<?php endwhile; ?>";
    }
}
