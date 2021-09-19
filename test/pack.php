<?php declare(strict_types = 1);

/**
 * This is the basic project/configuration file for phpack
 * Here you define the basic information about your app
 *
 * All paths in this file will be relative to this file
 * NO absolute paths are allowed in this file
 * 
 * For a full list of accepted keys/values check: 
 *      - https://github.com/cornejong/phpack/blob/main/readme.md#pack.php
 */

return [
    'accessor' => 'test',     // Basically the output filename
    'main' => 'main.php',         // The main entry file in your project

    'compress' => \Phar::GZ,        // The compression algo for the phar archive
    'checkSyntax' => true,          // If we check the syntax of the files included in the archive
    'noMinify' => [],               // Files/directories not to minimize
    'ignorable' => [],              // Files/directories not to include in the archive
];

