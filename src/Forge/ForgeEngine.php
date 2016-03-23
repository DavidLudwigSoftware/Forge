<?php

namespace Forge;


class ForgeEngine
{
    /**
     * The Forge environment
     * @var Forge\ForgeEnvironment
     */
    private $_env;

    /**
     * The Forge cache
     * @var Forge\ForgeCache
     */
    private $_cache;


    /**
     * The Forge Engine constructor
     * @param Forge\ForgeEnvironment $env The environment for Forge
     */
    public function __construct(ForgeEnvironment $env)
    {
        $this->_env = $env;

        if ($env->cachePath())

            $this->_cache = new ForgeCache($env);
    }

    /**
     * Load a php template
     * @param  string $path The path to the php file
     * @return string       The processed php file
     */
    protected function load(string $path)
    {
        ob_start();

        require $path;

        return ob_get_clean();
    }

    /**
     * Load a view from the given name
     * @param  string $view The name of the view
     * @return array        An array of the view's parts
     */
    public function loadView(string $view)
    {
        $name = str_replace('.', '/', $view);

        $content = $this->load(
            $this->environment()->templatePath() . "/$name.forge.php"
        );

        return $this->parse($content);
    }

    /**
     * Load a Forge Template
     * @param  string $view        The name of the view
     * @return Forge\ForgeTemplate A ForgeTemplate containing the view
     */
    public function loadTemplate(string $view)
    {
        if ($this->cache() && $this->cache()->exists($view))
        {
            $content = $this->cache()->load($view);
        }

        else
        {
            $content = $this->loadView($view);

            $content = $this->compile($content);

            if ($this->cache())

                $this->cache()->cache($view, $content);
        }

        return new ForgeTemplate($content);
    }

    /**
     * The current ForgeCache instance
     * @return Forge\ForgeCache
     */
    public function cache()
    {
        return $this->_cache;
    }

    /**
     * The current ForgeEnvironment instance
     * @return Forge\ForgeEnvironment
     */
    public function environment()
    {
        return $this->_env;
    }

    /**
     * Parse content from a string
     * @param  string $content The content containing Html and Forge code
     * @return array           An array of the view's parts
     */
    public function parse(string $content)
    {
        $content = $content ?: '';

        $parser = new ForgeParser($this);

        return $parser->parse($content);
    }

    /**
     * Compile a view's parts into a string
     * @param  array $parts An array of the view's parts
     * @return string       The parts compiled into an Html string
     */
    public function compile(array $parts)
    {
        return implode('', $parts);
    }
}
