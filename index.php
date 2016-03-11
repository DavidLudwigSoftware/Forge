<?php

require __DIR__ . '/vendor/autoload.php';

$env = new Forge\ForgeEnvironment([
    'template_path' => __DIR__ . '/views',
    'cache' => __DIR__ .'/cache'
]);

$forge = new Forge\ForgeEngine($env);

$template = $forge->loadTemplate('index');

$html = $template->render(['test' => 5]);

echo $html;
