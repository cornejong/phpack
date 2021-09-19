<?php

/*
 * File: Config.php
 * Project: KickStart
 * File Created: Wednesday, 18th September 2019 11:26:28 pm
 * Author: Corné de Jong (corne@tearo.eu)
 * ------------------------------------------------------------
 * Last Modified: Monday, 9th March 2020 5:33:03 pm
 * Modified By: Corné de Jong (corne@tearo.eu>)
 * ------------------------------------------------------------
 * Copyright 2019 - 2020 SouthCoast
 */

namespace SouthCoast\KickStart;

use SouthCoast\Helpers\ArrayHelper;
use Exception;

class Config
{
    public static $data = [];

    public static function load(string $configDirectory)
    {
        if (!is_dir($configDirectory)) {
            throw new Exception('Provided path is not a directory! Provided: ' . $configDirectory, 1);
        }

        foreach (glob(rtrim($configDirectory, '/') . '/*.php') as $file) {
            self::$data[lcfirst(basename($file, '.php'))] = require $file;
        }

        self::$data = ArrayHelper::flatten(self::$data);

        return true;
    }

    public static function exists(string $identifier): bool
    {
        return isset(self::$data[$identifier]);
    }

    public static function set(string $identifier, $value): bool
    {
        self::$data[$identifier] = $value;

        return self::$data[$identifier] === $value;
    }

    public static function get(string $identifier, $ifNotValue = null)
    {
        /* if (!self::exists($identifier)) {
            return $ifNotValue;
        } */

        return ArrayHelper::get($identifier, self::$data) ?? $ifNotValue;
    }
}
