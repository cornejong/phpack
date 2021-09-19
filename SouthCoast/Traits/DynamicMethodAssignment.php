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

use Closure;

/**
 * Trait providing dynamic method assignment for objects.
 */
trait DynamicMethodAssignment
{
    protected $methodCollection = [];

    public function __call($name, $arguments)
    {
        if (!array_key_exists($name, $this->methodCollection)) {
            throw new \RuntimeException("Method {$name} does not exist");
        }

        return call_user_func_array($this->methodCollection[$name], $arguments);
    }
   
    public function setMethod(string $name, $method) : bool
    {
        if (!is_callable($method)) {
            throw new \RuntimeException("Provided method is not callable!");
        }
        
        return $this->methodCollection[$name] = $method instanceof Closure ? $closure->bindTo($this, $this) : $method;
    }
}
