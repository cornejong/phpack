<?php

namespace SouthCoast\Storage;

use SouthCoast\KickStart\Env;
use SouthCoast\Helpers\ArrayHelper;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Exception;

class Parameter
{
    const STORAGE_PATH = 'Storage/Parameters';
    
    protected static $parameters = [];

    public static function init()
    {
        $directoryStructure = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(Env::base_dir() . DIRECTORY_SEPARATOR . Parameter::STORAGE_PATH));

        foreach ($directoryStructure as $file) {
            $pathInfo = \pathinfo($file);
            if ($pathInfo['extension'] !== 'php') {
                self::$parameters[strtolower($pathInfo['filename'])] = require $file;
            }
        }

        self::$parameters = ArrayHelper::flatten(ArrayHelper::sanitize(self::$parameters));

        return true;
    }

    public static function getParameterFileLocation(string $identifier) : string
    {
        $identifierArray = explode('.', $identifier);

        $vendor = strtolower(array_shift($identifierArray));
        
        return Env::base_dir() . DIRECTORY_SEPARATOR . Parameter::STORAGE_PATH . DIRECTORY_SEPARATOR . $vendor . DIRECTORY_SEPARATOR . implode('.', $identifierArray) . '.php';
    }

    public static function load(string $set = null) : bool
    {
        if (is_null($set)) {
            return self::init();
        }
        
        if (\file_exists(self::getParameterFileLocation($set))) {
            throw new Exception('Parameter File doesn\'t exists!', 1);
        }
        
        self::$parameters[strtolower(pathinfo($file)['filename'])] = require $file;
        self::$parameters = ArrayHelper::flatten(ArrayHelper::sanitize(self::$parameters));

        return true;
    }

    public static function get(string $identifier)
    {
        if (ArrayHelper::get($identifier, self::$parameters) === null) {
            self::load($identifier);
        }

        $data = ArrayHelper::get($identifier, self::$parameters);

        if ($data[Env::environment()] !== 'production' && isset($data['on' . ucfirst(Env::environment())]) && !empty('on' . ucfirst(Env::environment()))) {
            $result = array_merge($data['parameters'], $data['on' . ucfirst(Env::environment())]);
        }

        return $result ?? $data['parameters'];
    }

    public function __callStatic(string $vendor, $set)
    {
        if (empty($set)) {
            throw new Exception('Provide a Parameter set!', 1);
        }
        
        return Parameter::get($vendor . '.' . implode('.', $set));
    }
}
