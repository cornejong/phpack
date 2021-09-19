<?php

namespace SouthCoast\Console\Prepared\Commands;

use SouthCoast\Helpers\StringHelper;
use SouthCoast\Console\Format;
use SouthCoast\Console\Console;
use SouthCoast\Console\CommandMap;
use SouthCoast\Console\Abstracted\Command;

class HelpCommand extends Command
{
    public $name = 'Help Menu';
    public $description = 'An overview of all available commands and how to access them.';
    
    public $accessor = 'help';
    public $acceptedFlags = [
        '-b' => 'Include build info in help menu'
    ];

    public function boot()
    {
        # code...
    }

    public function execute()
    {
        if(($this->flags['b'] ?? false) === true) {
            Console::log('Build: ' . BUILD_ID . ' - Time: ' . BUILD_TIME . ' - Number: ' . BUILD_NUMBER . ' - Target: ' . BUILD_TARGET . PHP_EOL);
        }
        
        Console::log('Welcome to the ' . \App\App::get()->name . ' help menu.');
        Console::log('This are the currently supported commands:');
        Console::newLine();

        $array = [];
        
        foreach (CommandMap::export() as $command) {
            $command = new $command;
            if($command->hidden) {
                continue;
            }
        
            Console::log(StringHelper::pad(25, '[ ' . $command->name . ' ]') . '  $ ' . \App\App::get()->accessor . ' ' . $command->accessor);

            Console::log(Format::leftPad(28, "> " . $command->description));
            
            if(!empty($command->acceptedFlags ?? [])) {
                Console::log(Format::leftPad(28, "- Flags:"));
                foreach ($command->acceptedFlags ?? [] as $flag => $description) {
                    Console::log(Format::leftPad(30, $flag . ': ' . $description));
                }
            }
            
            Console::newLine();
        }

        return Command::SUCCESS;
    }
}
