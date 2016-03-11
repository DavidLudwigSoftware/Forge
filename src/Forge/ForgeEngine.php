<?php

namespace Forge;


class ForgeEngine
{
    private $_env;
    private $_cache;

    public function __construct(ForgeEnvironment $env)
    {
        $this->_env = $env;

        if ($env->cache())

            $this->_cache = new ForgeCache($env);
    }

    protected function load($path)
    {
        ob_start();

        require $path;

        return ob_get_clean();
    }

    public function loadView($view)
    {
        $content = $this->load(
            $this->environment()->path($view)
        );

        return $this->parse($content);
    }

    public function loadTemplate($view)
    {
        if ($this->cache() && $this->cache()->exists($view))

            $content = $this->cache()->load($view);

        else
        {
            $content = $this->loadView($view);

            $content = $this->compile($content);

            if ($this->cache())

                $this->cache()->cache($view, $content);
        }

        return new ForgeTemplate($content);
    }

    public function cache()
    {
        return $this->_cache;
    }

    public function environment()
    {
        return $this->_env;
    }

    public function parse(string $content)
    {
        $content = $content ?: '';

        $parser = new ForgeParser($this);

        return $parser->parse($content);
    }

    public function compile($parts)
    {
        return implode('', $parts);
    }
}
