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
            'asset_path'    => __DIR__,
            'cache_path'    => Null,
            'template_path' => __DIR__,
        ];

        foreach ($options as $key => $value)
        {
            $this->_options[$key] = rtrim($value, '/');
        }
    }

    /**
     * Return the template path
     * @return string
     */
    public function templatePath()
    {
        return $this->_options['template_path'];
    }

    /**
     * Return the cache (null if no cache is set)
     * @return string|null
     */
    public function cachePath()
    {
        return $this->_options['cache_path'];
    }

    /**
     * Return the asset path
     * @return string
     */
    public function assetPath()
    {
        return $this->_options['asset_path'];
    }
}
