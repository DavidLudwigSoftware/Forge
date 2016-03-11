<?php

namespace Forge\Property;

use Forge\ForgeParserMarker;

class SectionProperty extends \Forge\ForgeProperty
{
    public function section($params)
    {
        if ($this->storage()->get('section'))

            throw new ErrorException("You can't select a section within a section");

        $name = $this->parseParams($params);

        $this->storage()->store('section', $name);

        $this->parser()->newBuffer();
    }

    public function endsection()
    {
        $name = $this->storage()->get('section');

        if ($name === Null)

            throw new ErrorException("Unexpected 'endsection'");

        $this->storage()->remove('section');

        $content = $this->parser()->closeBuffer();

        $this->parser()->fillMark('section', $name, $content);
    }

    public function yield($params)
    {
        $name = $this->parseParams($params);

        $this->parser()->mark('section', $name);
    }
}
