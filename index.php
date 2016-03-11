<?php

// Import the Composer Autoloader
require __DIR__ . '/vendor/autoload.php';


// Create the Forge environment
$env = new Forge\ForgeEnvironment([
    'template_path' => __DIR__ . '/views',
    // 'cache' => __DIR__ .'/cache'
]);


// Create a ForgeEngine instance
$forge = new Forge\ForgeEngine($env);


// Load a template
$template = $forge->loadTemplate('index');


// Render a template into Html
$html = $template->render(['test' => 5, 'myArray' => [1, 2, 3, 4, 5]]);


// Print the Html
echo $html;
