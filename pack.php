<?php

/**
 * This is the basic project/configuration file for phpack
 * Here you define the basic information about your app
 *
 * All paths in this file will be relative to this file
 * NO absolute paths are allowed in this file
 */

return [
    'accessor' => 'phpack', // Basically the output filename
    'main' => 'main.php', // The main entry file in your project
    'compress' => Phar::GZ,
    'ignorable' => ['composer.lock', 'run', 'test'],
    'noMinify' => ['templates'],
];
