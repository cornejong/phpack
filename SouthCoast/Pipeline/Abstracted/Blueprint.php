<?php

/*
 * File: Blueprint.php
 * Project: Pipeline
 * File Created: Sunday, 8th March 2020 1:33:30 pm
 * Author: Corné de Jong (corne@tearo.eu)
 * ------------------------------------------------------------
 * Last Modified: Wednesday, 11th March 2020 11:12:33 am
 * Modified By: Corné de Jong (corne@tearo.eu>)
 * ------------------------------------------------------------
 * Copyright 2019 - 2020 SouthCoast
 */

namespace SouthCoast\Pipeline\Abstracted;

use SouthCoast\Pipeline\Pipeline;
use SouthCoast\Pipeline\Interfaces\Pipe;
use SouthCoast\Pipeline\Interfaces\Blueprint as BlueprintInterface;
use Generator;

/**
 * undocumented class
 */
abstract class Blueprint implements BlueprintInterface, Pipe
{
    const RETURN_METHOD_NAME = 'returns';
    const CHECK_METHOD_NAME = 'checks';

    protected $pipes = [];

    protected $returnPipe = false;
    protected $checkPipe = false;

    public function hasCheck(): bool
    {
        return method_exists(static::class, 'check');
    }

    public function hasSeal(): bool
    {
        return method_exists(static::class, 'seal');
    }

    public function pipes()
    {
        foreach ($this->pipes as $pipe) {
            yield $pipe;
        }
    }

    public function __invoke($payload)
    {
        return self::run($payload);
    }

    public static function run($payload = null)
    {
        return Pipeline::open(new static)->send($payload)->run();
    }

    public function continue(int $step, $payload = null)
    {
        # code...
    }
}
