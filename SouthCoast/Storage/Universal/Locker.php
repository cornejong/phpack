<?php

namespace SouthCoast\Storage\Universal;

use SouthCoast\Storage\Locker as LockerInstance;

class Locker
{
    public static $locker = null;

    public static function init()
    {
        self::$locker = new LockerInstance;
    }

    public static function add(string $identifier, $value)
    {
        if (self::$locker === null) {
            self::init();
        }

        return self::$locker->add($identifier, $value);
    }

    public static function get(string $identifier, $ifNotValue = null)
    {
        if (self::$locker === null || !self::$locker->exists($identifier)) {
            return $ifNotValue;
        }

        return self::$locker->get($identifier);
    }

    public static function remove(string $identifier)
    {
        return self::$locker->remove($identifier);
    }

    public function find(string $query, $ifNotValue = null)
    {
        if (self::$locker === null) {
            return $ifNotValue;
        }

        return self::$locker->find($query, $ifNotValue);
    }
}
