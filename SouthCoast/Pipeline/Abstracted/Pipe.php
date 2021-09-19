<?php

/*
 * File: Pipe.php
 * Project: Pipeline
 * File Created: Sunday, 8th March 2020 1:24:54 pm
 * Author: Corné de Jong (corne@tearo.eu)
 * ------------------------------------------------------------
 * Last Modified: Wednesday, 11th March 2020 11:12:52 am
 * Modified By: Corné de Jong (corne@tearo.eu>)
 * ------------------------------------------------------------
 * Copyright 2019 - 2020 SouthCoast
 */

namespace SouthCoast\Pipeline\Abstracted;

use SouthCoast\Pipeline\Interfaces\Pipe as PipeInterface;

abstract class Pipe implements PipeInterface
{
    abstract protected function handle($payload);

    public function __invoke($payload)
    {
        $originalPayload = $payload;

        if (method_exists($this, 'boot')) {
            $payload = $this->boot($payload);
            
            if ($payload === false) {
                return;
            }

            if ($payload === null) {
                $payload = $originalPayload;
            }
        }

        return $this->handle($payload);
    }

    public static function run($payload)
    {
        return (new static)->handle($payload);
    }
}
