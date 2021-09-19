<?php

namespace App;

use SouthCoast\Console\Prepared\Commands\HelpCommand;
use SouthCoast\Console\Abstracted\App as AbstractApp;
use App\Middleware\PrintApplicationHeader;
use App\Commands\UpdateCommand;
use App\Commands\RootCommand;
use App\Commands\InstallCommand;
use App\Commands\InitCommand;
use App\Commands\DeployCommand;
use App\Commands\BuildCommand;

class App extends AbstractApp
{
    public $name = 'phpack';
    public $version = '1.0.0';
    public $description = 'Easily package php cli apps as phar archives';

    public $accessor = 'phpack';

    public $projectFilename = 'pack.php';

    public static $verbose = false;
    
    /**
     * Middleware that wil run before every command
     *
     * @var array
     */
    public $globalMiddleware = [
        // PrintApplicationHeader::class
    ];

    /**
     * Array of commands that are not resigning in the Env::root() . '/Commands' directory
     *
     * @var array
     */
    public $commands = [
        InitCommand::class,
        BuildCommand::class,
        RootCommand::class,
        InstallCommand::class,
        UpdateCommand::class,
        HelpCommand::class,
    ];
}
