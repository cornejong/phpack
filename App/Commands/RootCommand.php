<?php

namespace App\Commands;

use SouthCoast\Console\Console;
use SouthCoast\Console\Abstracted\Command;
use PhpSchool\CliMenu\CliMenu;
use PhpSchool\CliMenu\Builder\CliMenuBuilder;

class RootCommand extends Command
{
    public $name = 'Symlink: Build';
    public $description = 'Features the same functionality as $ phpack build';
    
    public $accessor = '';
    public $acceptedFlags = [];

    public $hidden = true;
    
    public $middleware = [
        // PathExistsMiddleware::class
    ];

    public function boot()
    {
        # code...
    }

    public function execute()
    {
        $command = new BuildCommand();
        return $command();
    }

    public function shutdown()
    {
        # code...
    }
}
