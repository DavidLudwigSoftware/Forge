<?php

namespace Forge\Property;

use Forge\ForgeToken as Token;

class ForElseProperty extends \Forge\ForgeProperty
{
    public function forelse($params)
    {
        $this->incLevel();

        $this->storeParams($params);

        return "<?php foreach($params): ?>";
    }

    public function empty()
    {
        $array = $this->storage()->get($this->level());
        return "<?php endforeach;if(count($array) == 0): ?>";
    }

    public function endforelse()
    {
        $this->decLevel();
        return "<?php endif; ?>";
    }

    public function level()
    {
        $this->storage()->store('forelse_level', 0);
    }

    public function incLevel()
    {
        $level = $this->level();

        $this->storage()->store('forelse_level', $level + 1);
    }

    public function decLevel()
    {
        $level = $this->level();

        $this->storage()->store('forelse_level', $level - 1);
    }

    public function storeParams($params)
    {
        $array = trim(explode('as', $params)[0]);

        $this->storage()->store($this->level(), $array);
    }
}
