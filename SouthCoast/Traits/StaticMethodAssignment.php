<?php

/*
 * File: DynamicMethodDefinition.php
 * Project: Traits
 * File Created: Wednesday, 25th March 2020
 * Author: Corné de Jong (corne@tearo.eu)
 * ------------------------------------------------------------
 * Copyright 2019 - 2020 SouthCoast
 */

namespace SouthCoast\Traits;

/**
 * Trait providing static method assignment for objects.
 */
trait StaticMethodAssignment
{
    protected static $staticMethodCollection = [];

    public static function __staticCall($name, $arguments)
    {
        if (!array_key_exists($name, self::$staticMethodCollection)) {
            throw new \RuntimeException("Method {$name} does not exist");
        }

        return call_user_func_array(self::$staticMethodCollection[$name], $arguments);
    }
   
    public static function setStaticMethod($name, $closure) : bool
    {
        if (!is_callable($method)) {
            throw new \RuntimeException("Provided method is not callable!");
        }
        
        return self::$staticMethodCollection[$name] = $closure;
    }
}
