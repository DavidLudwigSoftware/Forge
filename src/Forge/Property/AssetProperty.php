<?php

namespace Forge\Property;

use Forge\ForgeToken as Token;

class AssetProperty extends \Forge\ForgeProperty
{
    private static $_dependencies;

    private function assetPath(string $asset)
    {
        $ext = pathinfo($asset)['extension'];

        $path = $this->forge()->environment()->assetPath();

        return $path . "/$ext/$asset";
    }

    public function javascript(string $script)
    {
        $path = $this->assetPath($this->parseParams($script) . '.js');

        return "<script src=\"$path\", type=\"text/javascript\"></script>";
    }

    public function stylesheet(string $styleSheet)
    {
        $path = $this->assetPath($this->parseParams($styleSheet) . '.css');

        return "<link href=\"$path\", type=\"text/css\" rel=\"stylesheet\">";
    }

    public function dependency(string $dependencies)
    {
        if (!static::$_dependencies)

            static::$_dependencies = require __DIR__ . '/../Config/dependencies.php';

        $params = $this->parseParams("[$dependencies]");

        $html = '';

        foreach ($params as $param)

            $html .= static::$_dependencies[$param] . "\n";

        return $html;
    }
}
