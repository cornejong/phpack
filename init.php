<?php declare(strict_types = 1);

require __dir__ . '/vendor/autoload.php';

use SouthCoast\KickStart\Env;
use SouthCoast\KickStart\Config;
use SouthCoast\Console\ErrorHandler;
use SouthCoast\Console\Console;

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    Console::error('Windows is currently not supported!');
}

Config::load(Env::isBuild() ? __dir__ . '/App/Config' : __dir__ . '/App/Config');

Config::set('app.root', Env::isBuild() ? 'self.phar' : __dir__ . '/App');
Config::set('build.root', Env::isBuild() ? 'self.phar' : __dir__);

Config::set('user.dir', trim(Console::run('echo $HOME')));
Config::set('runtime.cwd', trim(Console::run('cwd')));

/* Or the path defined by this method, the path defined by this method will be leading! */
ErrorHandler::setApplicationRoot(Config::get('build.root'));
/* register the handler */
ErrorHandler::register();
 
if (file_exists(Config::get('app.root') . '/Register.php')) {
    /* Require the register file for module registration */
    require Config::get('app.root') . '/Register.php';
}

function app() {
    return App\App::$instance;
}