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

class initCommand extends Command
{
    public $name = 'Initialize';
    public $description = 'Initialize phpack in a project.';
    
    public $accessor = 'init';
    public $acceptedFlags = [
        '--no-loader' => 'Don\'t include the default autoloader'
    ];
    
    public $middleware = [
        // PathExistsMiddleware::class
    ];

    public function boot()
    {
        Console::log('> ' . Color::blue('phpack') . ' => initialize project' . PHP_EOL);

        if (\file_exists(getcwd() . '/' . app()->projectFilename)) {
            Console::info('Project already initialized!');
            Console::exit(0);
        }

    }

    public function execute()
    {
        $currentDirectoryName = \basename(getcwd());
        $accessor = Console::ask('Output filename? (' . $currentDirectoryName . ') : ');
        if(empty($accessor)) {
            $accessor = $currentDirectoryName;
        }
        $len = \strlen('Output filename? (' . $currentDirectoryName . ') : ');
        // Confirm the entered value
        Console::log(Color::blue($accessor));
        
        
        $main = Console::ask('Application entry? (main.php) : ');
        if (empty($main)) {
            $main = 'main.php';
        }
        // Confirm the entered value
        Console::log(Color::blue($main));

        Console::log(PHP_EOL . '> Setting up basic project structure...');

        $templatePath = Config::get('build.root') . '/templates';

        $sortCallback = function ($a, $b) {
            return strlen($b) - strlen($a);
        };
        
        $files = File::recursiveList($templatePath);
        usort($files, $sortCallback);

        $mainContent = '';

        foreach($files as $fileLocation) {
            if(!\file_exists($fileLocation)) {
                Console::error('Could not find template file! ' . $fileLocation);
                continue;
            }

            $filePath = str_replace('{accessor}', $accessor, $fileLocation);
            $filePath = \str_replace($templatePath, '', $filePath);
            $subPath = dirname($filePath);

            if($filePath === '/boot.php') {
                $mainContent = \file_get_contents($fileLocation);
                continue;
            }

            if ($filePath === '/autoloader.php' && ($this->flags['no-loader'] ?? false) === true) {
                continue;
            }
            
            if(file_exists(getcwd() . $filePath)) {
                Console::log('> Skipping .' . $filePath . ' (already exists)');
                continue;
            }

            if(!file_exists(rtrim(getcwd() . $subPath, '/'))) {
                mkdir(rtrim(getcwd() . $subPath, '/'), 0777, true);
            }
            
            $content = file_get_contents($fileLocation);
            if (!empty($content)) {
                $content = str_replace('{main}', $main, $content);
                $content = str_replace('{accessor}', $accessor, $content);
            }
            
            Console::log('> Creating .' . $filePath);
            \file_put_contents(getcwd() . $filePath, $content, LOCK_EX);

            if(\file_exists(getcwd() . $filePath)) {
                Console::replaceLastLine(Color::green('✓') . ' Created .' . $filePath);
            } else {
                Console::replaceLastLine(Color::red('✖') . ' Failed to create .' . $filePath);
            }
        }

        if(!\file_exists(getcwd() . '/' . $main)) {
            Console::log('> Creating ./' . $main);

            if(($this->flags['no-loader'] ?? false) === true) {
                $mainContent = "<?php declare(strict_types = 1);\n\n/**\n*--------------------------------------------------------------------------\n* Register Composer Auto Loader\n*--------------------------------------------------------------------------\n*\n* Composer provides a convenient, automatically generated class loader for\n* any application. You just need to utilize it! You'll simply require it\n* into the script here so that we don't have to worry about manual\n* loading any of our classes later on. It feels great to relax.\n*\n*/\n\n// \$composer = require  'vendor/autoload.php';\n//\n// function &composer()\n// {\n//     global \$composer;\n//     return \$composer;\n// }\n\n\n# Only your imagination can bring this thing to live\n\r\n";
            }

            $mainContent = str_replace('{main}', $main, $mainContent);
            $mainContent = str_replace('{accessor}', $accessor, $mainContent);
            \file_put_contents(getcwd() . '/' . $main, $mainContent);
            
            if(\file_exists(getcwd() . '/' . $main)) {
                Console::replaceLastLine(Color::green('✓') . ' Created ./' . $main);
            } else {
                Console::replaceLastLine(Color::red('✖') . ' Failed to create ./' . $main);
            }
        }


        system('chmod +x "' . getcwd() . '/' . $accessor . '"');

        Console::log(PHP_EOL . '> Successfully initialized ' . Color::blue('phpack') . '! ');
        return Command::SUCCESS;
    }

    public function shutdown()
    {
        # code...
    }
}
