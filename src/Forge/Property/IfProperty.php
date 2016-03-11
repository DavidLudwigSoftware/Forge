<?php

namespace Forge\Property;

use Forge\ForgeToken as Token;

class IfProperty extends \Forge\ForgeProperty
{
    public function if($condition)
    {
        return "<?php if ($condition): ?>";
    }

    public function elif($condition)
    {
        return "<?php elseif ($condition): ?>";
    }

    public function else()
    {
        return "<?php else: ?>";
    }

    public function endif()
    {
        return "<?php endif; ?>";
    }
}
