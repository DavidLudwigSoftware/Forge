<?php

namespace Forge;


class ForgeCache
{
    private $_env;

    public function __construct(ForgeEnvironment $env)
    {
        $this->_env = $env;
    }

    public function exists(string $view)
    {
        return file_exists($this->_env->cachePath($view));
    }

    public function cache($view, $content)
    {
        echo "Caching...";
        $file = fopen($this->_env->cachePath($view), 'w');

        fwrite($file, $content);

        fclose($file);
    }

    public function load($view)
    {
        return file_get_contents($this->_env->cachePath($view));
    }
}
