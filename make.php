<?php

/**
 * This is a makephp example file.
 * You'd place this file in your project's root directory
 * 
 * If you call 'makephp' without an argument the app will call
 * the first function in this file.
 * 
 * otherwise the specified function will be called
 */

function main()
{
    echo "Not much going on in main\n";
}

function install() 
{
    return [
		__dir__ . '/run build debug',
		__dir__ . '/build/debug/phpack -y self:install'
	];
}

function init()
{
	return [
        './run build debug',
        './build/debug/phpack -y self:install'
    ];

}

?>