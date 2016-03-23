<?php

namespace Forge\PrintStatement;

class UnescapedPrint extends \Forge\ForgePrintStatement
{
    public function display()
    {
        return $this->content();
    }
}
