<?php

namespace SouthCoast\KickStart;

use SouthCoast\KickStart\Env;
use SouthCoast\KickStart\Config;
use SouthCoast\Helpers\Json;
use SouthCoast\Helpers\ArrayHelper;

class AutoPackageLoader
{
    const SPL_AUTOLOAD_HANDLER = self::class . '::' . 'handler';

    protected static $classMap = null;

    public static function initMigrateUp()
    {
        return;
    }

    public static function initMigrateDown()
    {
        return;
    }

    public static function register()
    {
        spl_autoload_register(self::SPL_AUTOLOAD_HANDLER);
    }

    public static function handler(string $class)
    {
        if (is_null(self::$classMap)) {
            self::loadClassMap();
        }

        if (!self::isAutoLoadable($class, $package)) {
            return;
        }

        /* Get the composer.json file */
        $composer = Json::parseToArray(file_get_contents(Env::root() . '/composer.json'));
        /* Add the required package */
        $composer['require'][array_shift($package)] = '*';
        /* Store the updated composer file */
        file_put_contents(Env::root() . '/composer.json', Json::prettyEncode($composer));

        /* Maybe also run the composer command, not sure yet */
        // Console::run('composer install', Env::root());

        /* Throw exception to inform the user */
        throw new \Exception('The class \'' . $class . '\' was not yet part of your project. Run \'composer install\' to add it.', 1);
    }

    public static function loadClassMap()
    {
        self::$classMap = ArrayHelper::flatten(Json::parseToArray(file_get_contents(Config::get('core.classMapLocation'))));
    }

    public static function isAutoLoadable(string $class, &$found = null)
    {
        return ArrayHelper::searchByQuery('?.classes.? = ' . $class, self::$classMap, $found);
    }

    public static function getPackageFromClass(string $class)
    {
        if (ArrayHelper::searchByQuery('?.classes.? = ' . $class, self::$classMap, $found)) {
            return $found;
        }

        return null;
    }
}
