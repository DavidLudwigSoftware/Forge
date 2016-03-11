<?php

namespace Forge;


class ForgeEnvironment
{
    private $_options;

    public function __construct(array $options = [])
    {
        $this->_options = [
            'template_path' => '/',
            'cache' => Null,
        ];

        $this->_options = array_merge($this->_options, $options);
    }

    public function path(string $view)
    {
        $path = $this->_options['template_path'];

        $file = str_replace('.', '/', $view) . '.forge.php';
        
        return $path . ($path[strlen($path) -1] != '/' ? '/' : '') . $file;
    }

    public function cachePath(string $view)
    {
        if ($path = $this->_options['cache'])
        {
            $file = md5($view) . '.forge.php';

            return $path . ($path[strlen($path) -1] != '/' ? '/' : '') . $file;
        }
    }

    public function cache()
    {
        return $this->_options['cache'];
    }
}
