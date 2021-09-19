<?php

namespace SouthCoast\KickStart;

use SouthCoast\Storage\Universal\Locker;
use SouthCoast\Helpers\Identifier;

class Event extends Locker
{
    public static $events = [];

    public static function register(string $eventId, callable $callback)
    {
        if (!is_callable($callback)) {
            throw new Exception('Provided callback is not callable!', 1);
        }

        $registerId = Identifier::newGuid();
        // self::$events[$eventId][$registerId] = $callback;
        parent::add(implode('.', [$eventId, $registerId]), $callback);

        return $registerId;
    }

    public static function registerAll(array $events) : array
    {
        $registerIds = [];

        foreach ($events as $eventId => $callback) {
            $registerIds[$eventId][] = self::register($eventId, $callback);
        }

        return $registerIds;
    }

    public static function unRegister(string $eventId, string $registerId)
    {
        $identifier = implode('.', [$eventId, $registerId]);
        if (parent::exists($identifier)) {
            parent::remove($identifier);
        }
    }

    public static function fire(string $eventId, &$eventParameters = [])
    {
        foreach (parent::get($eventId) ?? [] as $listener) {
            try {
                @call_user_func($listener, ...$eventParameters);
            } catch (\Throwable $th) {
                /* Lets not worry about other peoples code */
                if (Env::isDev()) {
                    /* Except when we're in development ^^ */
                    throw new $th;
                }
    
                continue;
            }
        }
    }
}
