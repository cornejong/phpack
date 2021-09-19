<?php

/*
 * File: IteratorAccess.php
 * Project: SouthCoast\Traits
 * File Created: Tuesday, 24th March 2020
 * Author: Corné de Jong (corne@tearo.eu)
 * ------------------------------------------------------------
 * Copyright 2019 - 2020 SouthCoast
 */

namespace SouthCoast\Traits;

/**
 * Trait providing all interfacing methods to implement Iterator
 *
 * Required is to specify a container pointer:
 * - protected $containerPointer = '{The name of the variable that you want to iterate over}';
 */
trait IteratorAccess
{
    protected $iteratorPosition = 0;

    public function __construct(...$arguments)
    {
        if (empty($this->containerPointer ?? null)) {
            throw new Exception('No container pointer provided!', 1);
        }

        parent::__construct(...$arguments);
    }
    
    public function rewind()
    {
        $this->iteratorPosition = 0;
    }

    public function current()
    {
        return $this->{$this->containerPointer}[$this->iteratorPosition];
    }

    public function key()
    {
        return $this->iteratorPosition;
    }

    public function next()
    {
        $this->iteratorPosition++;
    }

    public function valid()
    {
        return isset($this->{$this->containerPointer}[$this->iteratorPosition]);
    }
}
