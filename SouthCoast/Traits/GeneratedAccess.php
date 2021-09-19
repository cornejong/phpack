<?php

/*
 * File: GeneratedAccess.php
 * Project: SouthCoast\Traits
 * File Created: Tuesday, 24th March 2020 8:35:24 pm
 * Author: Corné de Jong (corne@tearo.eu)
 * ------------------------------------------------------------
 * Last Modified: Tuesday, 24th March 2020 9:20:28 pm
 * Modified By: Corné de Jong (corne@tearo.eu>)
 * ------------------------------------------------------------
 * Copyright 2019 - 2020 SouthCoast
 */

namespace SouthCoast\Traits;

/**
 * Trait providing generated access to a class variable.
 *
 * Required is to specify a container pointer:
 * - protected $containerPointer = '{The name of the variable that you want to access}';
 */
trait GeneratedAccess
{
    protected $containerPointer;
    
    public function __construct(...$arguments)
    {
        if (empty($this->containerPointer ?? null)) {
            throw new Exception('No container pointer provided!', 1);
        }

        parent::__construct(...$arguments);
    }

    public function generate()
    {
        foreach ($this->{$this->containerPointer} as $key => $value) {
            if (method_exists($this, 'preYieldHandler')) {
                yield $key => $this->preYieldHandler($value);
            } else {
                yield $key => $value;
            }
        }
    }
}
