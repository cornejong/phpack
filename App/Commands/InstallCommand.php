<?php

namespace App\Commands;

use SouthCoast\KickStart\Config;
use SouthCoast\Helpers\File;
use SouthCoast\Console\Format;
use SouthCoast\Console\Cursor;
use SouthCoast\Console\Console;
use SouthCoast\Console\Color;
use SouthCoast\Console\Abstracted\Command;
use App\Helpers\Locator;

class InstallCommand extends Command
{
    public $name = 'Install';
    public $description = 'Moves the executable to the /usr/bin directory';
    
    public $accessor = 'self:install';
    public $acceptedFlags = [
        '--bin' => 'The bin location you want the executable to be install in. (default: /usr/local/bin)',
        '-y' => 'Force all questions to be answered with "yes"'
    ];
    
    public $middleware = [
        // PathExistsMiddleware::class
    ];

    public function boot()
    {
        Console::log('> ' . Color::blue('phpack') . ' => install' . PHP_EOL);
    }

    public function execute()
    {
        $bin = $this->flags['bin'] ?? '/usr/local/bin';

        if(\file_exists($bin . '/phpack')) {
            $override = 'y';
            
            if(($this->flags['y'] ?? false) !== true) {
                $override = Console::ask('Do you want to override the existing phpack executable? (Y/N) : ');
            }
            
            if(strtolower($override) === 'n') {
                return Command::SUCCESS;
            }
            
            Console::newLine();

            $command = 'rm -f -v ' . $bin . '/phpack';
            Console::log($command);
            // Console::log(Color::blue('🔒 ') . 'Please enter your sudo password to remove the current version of phpack in /usr/local/bin');
            system($command, $status);
            Console::newLine();
        }

        $command = 'cp -v "' . \Phar::running(false) . '" /usr/local/bin';
        Console::log($command);
        // Console::log(Color::blue('🔒 ') . 'Please enter your sudo password to copy the current executable into /usr/local/bin');
        system($command, $status);
        
        $status === 0
            ? Console::log(PHP_EOL . '✓ Successfully installed '     . Color::blue('phpack') . '! ')
            : Console::log(PHP_EOL . '✖ Failed to install ' . Color::red('phpack') . '! ');
       
        return $status;
    }

    public function shutdown()
    {
        # code...
    }
}
