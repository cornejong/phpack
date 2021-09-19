<?php

namespace SouthCoast\KickStart;

use SouthCoast\Helpers\Json;
use SouthCoast\Helpers\ArrayHelper;

class Env
{
    const DEVELOPMENT = 'development';
    const TESTING = 'testing';
    const STAGING = 'staging';
    const PRODUCTION = 'production';

    const FILENAME = 'sc.env';

    const REQUIRED_ENV_VALUES = [
        'environment' => 'development|testing|staging|production',
        'identifier' => 'String | The Identifier for the current runtime. (development machine || developer name)',
        'root' => 'Path | The path to the base directory of the application'
    ];

    protected static $environment = [];
    protected static $env_path = '';

    public static function load(string $path) : bool
    {
        if (!is_readable($path)) {
            throw new EnvError(EnvError::NON_EXISTING_ENV_FILE, $path);
        }

        self::$env_path = $path;

        $content_array = require $path;

        if (ArrayHelper::requiredPramatersAreSet(Env::REQUIRED_ENV_VALUES, $content_array, $missing, true)) {
            throw new EnvError(EnvError::MISSING_REQUIRED_ENV_VALUES, $missing);
        }

        if (isset($content_array['ini'])) {
            self::setIni($content_array['ini'] ?? []);
            unset($content_array['ini']);
        }

        self::$environment = $content_array;

        return true;
    }

    public static function setIni(array $ini)
    {
        foreach ($ini as $name => $value) {
            ini_set($name, $value);
        }
    }

    public static function restoreIni()
    {
        ini_restore();
    }

    public static function simulateProduction()
    {
        self::$environment['environment'] = 'production';
    }

    public static function get(string $name)
    {
        return ArrayHelper::get($name, self::$environment);
    }

    public static function __callStatic(string $name, $arguments = null)
    {
        return self::contains($name) ? self::$environment[$name] : null;
    }

    public static function contains($name) : bool
    {
        return (ArrayHelper::get($name, self::$environment) !== null) ? true : false;
    }

    public static function isDev() : bool
    {
        return self::$environment['environment'] === 'development';
    }

    public static function isBuild()
    {
        return isset($BUILD);
    }

    public static function isConsole() : bool
    {
        return (defined('STDIN')) ? true : false;
    }

    public static function inConsole() : bool
    {
        return self::isConsole();
    }

    public static function isLoaded() : bool
    {
        return !empty(self::$environment) ? true : false;
    }
}


class EnvError extends \Error
{
    const OVERRIDE_PROTECTION = [
        'message' => 'Overriding of existing Environment data is not allowed!',
        "code" => 999
    ];

    const NON_EXISTING_ENV_FILE = [
        'message' => 'The provided path to the ' . Env::FILENAME . ' file is not reachable!! Path: ',
        'code' => 12
    ];

    const COULD_NOT_OPEN_STREAM = [
        'message' => 'Could not open stream to the ' . Env::FILENAME . ' file! Path: ',
        'code' => 15
    ];

    const MISSING_REQUIRED_ENV_VALUES = [
        'message' => 'There are required elements missing from your env file! Missing: ',
        'code' => 16
    ];

    const ENV_CONST_ALREADY_DEFINED = [
        'message' => 'One or more environment variables set in ' . Env::FILENAME . ' are already defined! Variable: ',
        'code' => 20
    ];

    const EXCEPTION_THROWN = [
        'message' => 'There was an Exception or Error Thrown! Message: ',
        'code' => 000
    ];

    public function __construct(array $error, $extra = null)
    {
        extract($error);

        if (!empty($extra)) {
            $message .= "\n" . Json::prettyEncode($extra);
        }

        parent::__construct($message, $code);
    }
}
