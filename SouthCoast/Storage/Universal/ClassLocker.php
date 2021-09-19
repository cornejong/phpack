<?php

namespace SouthCoast\Storage\Universal;

use SouthCoast\Storage\Universal\Locker;

class ClassLocker extends Locker
{
    public static function register(string $classString, string $classId)
    {
        if (!class_exists($classString)) {
            throw new Exception('Class "' . $classString . '" does not exists!', 1);
        }

        return parent::add($classId, $classString);
    }

    public static function getInstance(string $classId, $ifNotValue = null, ...$parameters)
    {
        $classString = parent::get($classId);

        return $classString !== null ? new $classString(...$parameters) : $ifNotValue;
    }
}
