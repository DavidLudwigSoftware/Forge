<?php

namespace Forge;


class ForgeCache
{
    /**
     * The Forge environment
     * @var Forge\ForgeEnvironment
     */
    private $_env;


    /**
     * Create a ForgeCache instance and store the Environment
     * @param Forge\ForgeEnvironment $env The Forge environment
     */
    public function __construct(ForgeEnvironment $env)
    {
        $this->_env = $env;
    }

    /**
     * Determine if a cached file exists for the view
     * @param  string $view The name of the view
     * @return bool         True|False if the view exists
     */
    public function exists(string $view)
    {
        return file_exists($this->_env->cachePath($view));
    }

    /**
     * Store a view into the cache
     * @param  string $view    The name of the view
     * @param  string $content The content of the view
     * @return void
     */
    public function cache(string $view, string $content)
    {
        $file = fopen($this->_env->cachePath($view), 'w');

        fwrite($file, trim($content));

        fclose($file);
    }

    /**
     * Load a view from the cache
     * @param  string $view The name of the view
     * @return string       The source code of the cached view
     */
    public function load(string $view)
    {
        return file_get_contents($this->_env->cachePath($view));
    }
}
