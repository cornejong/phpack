<?php

/*
 * File: Searchable.php
 * Project: SouthCoast\Traits
 * File Created: Tuesday, 24th March 2020 8:38:07 pm
 * Author: Corné de Jong (corne@tearo.eu)
 * ------------------------------------------------------------
 * Last Modified: Tuesday, 24th March 2020 9:19:38 pm
 * Modified By: Corné de Jong (corne@tearo.eu>)
 * ------------------------------------------------------------
 * Copyright 2019 - 2020 SouthCoast
 */

namespace SouthCoast\Traits;

/**
 * Trait providing search related functionality to a class variable.
 *
 * Required is to specify a container pointer:
 * - protected $containerPointer = '{The name of the variable that you want to search}';
 */
trait GeneratedAccess
{
    public function __construct(...$arguments)
    {
        if (empty($this->containerPointer ?? null)) {
            throw new Exception('No container pointer provided!', 1);
        }

        parent::__construct(...$arguments);
    }

    public function contains($value)
    {
        return isset(array_flip($this->{$this->containerPointer})[$value]);
    }
}
