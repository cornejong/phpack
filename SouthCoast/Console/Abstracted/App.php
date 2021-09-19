<?php

namespace SouthCoast\Console\Abstracted;

use SouthCoast\KickStart\Env;
use SouthCoast\KickStart\Config;
use SouthCoast\Helpers\File;
use SouthCoast\Console\Prepared\Commands\HelpCommand;
use SouthCoast\Console\Console;
use SouthCoast\Console\CommandMap;
use SouthCoast\Console\Color;

abstract class App
{
    public static $instance = null;
    
    public $globalMiddleware = [];

    protected $runtime = [
        'statusCode' => 0
    ];

    public function __construct()
    {
        self::$instance = &$this;

        $commands = File::recursiveList(Config::get('app.root') . '/Commands', '/\.php$/');

        foreach ($commands ?? [] as $file) {
            $class = rtrim(str_replace(Config::get('app.root') . '/Commands/', 'App\\Commands\\', $file), '.php');
            $class = str_replace('/', '\\', $class);

            if (empty($class)) {
                continue;
            }


            CommandMap::add(new $class);
        }

        foreach ($this->commands ?? [] as $class) {
            CommandMap::add(new $class);
        }
    }

    public function getGlobalMiddleware()
    {
        return $this->globalMiddleware;
    }

    public function setStatusCode(int $code)
    {
        $this->runtime['statusCode'] = $code;
    }

    public function run()
    {
        Console::loadEnv();
        
        /* Check if the route is known, and pass $route by reference */
        /* This will be filled when a route is found, stays empty when not */
        if (!CommandMap::contains(Console::envString(), $command)) {
            /* Not found */
            Console::log(Color::red('✖ Command not found! run $ help for all commands'));
            Console::exit();
        }

        if (!$command->passesMiddleware($response)) {
            /* Do something with that response */
            Console::log($response);
            Console::exit(2);
        }

        try {
            if ($command->__invoke() !== Command::SUCCESS) {
                /* Do some error ish ting */
                Console::log(Color::red('✖ Exited with Command::FAILED'));
            }
        } catch (\Throwable $th) {
            throw $th;
        }

        Console::exit($this->runtime['statusCode']);
    }

    public static function get()
    {
        return self::$instance;
    }
}
