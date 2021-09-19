<?php

namespace SouthCoast\Console;

use SouthCoast\KickStart\Env;
use SouthCoast\Console\Prepared\Commands\HelpCommand;
use SouthCoast\Console\Abstracted\Command;

class CommandMap
{
    public static $commands = [];
    public static $variables = [];

    public static function add(Command $command)
    {
        self::$commands = array_merge(self::$commands, $command->export());
    }

    public static function load(bool $forceReload = false)
    {
        if (!Env::isDev() || $forceReload) {
            self::$routes = require Env::cacheLocation() . '/commandMap.cached.sw';
            return;
        }

        $commands = File::listRecursive(Env::root() . '/Commands');
        
        foreach ($commands as $file) {
            $class = rtrim(str_replace(Env::root(), 'App\\Commands\\', str_replace('/', '\\', $file)), '.php');
            self::add(new $class);
        }

        self::add(new HelpCommand(self::$commands));
    }

    public static function export()
    {
        return self::$commands;
    }

    public static function contains(string $environment, &$command) : bool
    {
        return !is_null(($command = self::findMatch($environment)));
    }

    public static function findMatch(string $argumentString)
    {
        foreach (self::$commands as $pattern => $command) {
            if (preg_match($pattern, $argumentString, $commandArguments)) {
                array_shift($commandArguments);

                $object = new $command;
                $object->loadCommandArguments($commandArguments);

                return $object;
            }
        }

        return null;
    }
}
