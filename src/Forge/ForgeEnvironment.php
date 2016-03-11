<?php

namespace Forge;


class ForgeEnvironment
{
    /**
     * Store the environment's optinos
     * @var array
     */
    private $_options;

    /**
     * Create a Forge environment
     * @param array $options A list of options for Forge
     */
    public function __construct(array $options = [])
    {
        $this->_options = [
            'template_path' => '/',
            'cache' => Null,
        ];

        $this->_options = array_merge($this->_options, $options);
    }

    /**
     * Get the path to a view
     * @param  string $view The name of the view
     * @return string       The absolute path to the view
     */
    public function path(string $view)
    {
        $path = $this->_options['template_path'];

        $file = str_replace('.', '/', $view) . '.forge.php';

        return $path . ($path[strlen($path) -1] != '/' ? '/' : '') . $file;
    }

    /**
     * Get the cache path to a view
     * @param  string $view The name of the view
     * @return string       The absolute path to the view
     */
    public function cachePath(string $view)
    {
        if ($path = $this->_options['cache'])
        {
            $file = md5($view) . '.forge.php';

            return $path . ($path[strlen($path) -1] != '/' ? '/' : '') . $file;
        }

        return '';
    }

    /**
     * Return the cache (used to determine if cache is enabled)
     * @return string|null
     */
    public function cache()
    {
        return $this->_options['cache'];
    }
}
