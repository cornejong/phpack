<?php

namespace App\Commands;

use SouthCoast\Console\Console;
use SouthCoast\Console\Abstracted\Command;
use PhpSchool\CliMenu\CliMenu;
use PhpSchool\CliMenu\Builder\CliMenuBuilder;

class UpdateCommand extends Command
{
    public $name = 'Update';
    public $description = 'Updates the current executable to the latest version';
    
    public $accessor = 'self:update';
    public $acceptedFlags = [];
    
    public $middleware = [
        // PathExistsMiddleware::class
    ];

    public function boot()
    {
        # code...
    }

    public function execute()
    {
        $tmp = sys_get_temp_dir();
        $bin = str_replace('/phpack', '', THIS);

        system('cd "' . $tmp . '" && curl https://get.phpack.dev/latest -o phpack', $status);
        if($status > 0) {
            Console::error('Could not download latest version! dl: https://get.phpack.dev/latest');
        }
        
        system('cd "' . $tmp . '" && chmod +x phpack', $status);
        if ($status > 0) {
            Console::error('Could not change permissions for executable!');
        }

        system('cd "' . $tmp . '" && ./phpack self install -y --bin="' . $bin . '"', $status);
        if ($status > 0) {
            Console::error('Could not move latest version to current bin! bin: ' . $bin);
        }

        Console::success('Successfully updated phpack to the latest version');
        return Command::SUCCESS;
    }

    public function shutdown()
    {
        # code...
    }
}
