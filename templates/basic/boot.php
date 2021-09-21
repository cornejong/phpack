<?php declare(strict_types = 1);


define('EXECUTION_START', microtime(true));


/**
*--------------------------------------------------------------------------
* Register The Auto Loader
*--------------------------------------------------------------------------
*
* The boiler plate comes with this simple and streamlined autoloader.
* Great for smaller/simpler applications that are not dependent on
* a large set of dependencies. If so, don't bother with composer, this 'll do fine.
*
*/

require 'autoloader.php';

$autoloader = new Autoloader([
    /* namespace => directory */
    'App' => 'app/'
]);

/* Now register the autoloader */
$autoloader->register(true, true);

function &autoloader() {
    global $autoloader;
    return $autoloader;
}


/**
*--------------------------------------------------------------------------
* Register Composer Auto Loader
*--------------------------------------------------------------------------
*
* Composer provides a convenient, automatically generated class loader for
* any application. You just need to utilize it! You'll simply require it
* into the script here so that we don't have to worry about manual
* loading any of our classes later on. It feels great to relax.
*
*/

// $composer = require  'vendor/autoload.php';
// 
// function &composer()
// {
//     global $composer;
//     return $composer;
// }


/**
*--------------------------------------------------------------------------
* Your Application
*--------------------------------------------------------------------------
*
* And last, but most certainly not least, your app.
* Go crazy, have fun, enjoy, create and share.
*
*/

