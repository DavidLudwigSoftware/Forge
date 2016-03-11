<?php

namespace Forge\PrintStatement;

class EscapedPrint extends \Forge\ForgePrintStatement
{
    public function display()
    {
        return "htmlentities(" . $this->content() . ")";
    }
}
