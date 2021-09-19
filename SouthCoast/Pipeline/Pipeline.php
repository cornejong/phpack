<?php

/*
 * File: Pipeline.php
 * Project: Pipeline
 * File Created: Sunday, 8th March 2020 1:24:36 pm
 * Author: Corné de Jong (corne@tearo.eu)
 * ------------------------------------------------------------
 * Last Modified: Tuesday, 10th March 2020 1:33:10 pm
 * Modified By: Corné de Jong (corne@tearo.eu>)
 * ------------------------------------------------------------
 * Copyright 2019 - 2020 SouthCoast
 */


namespace SouthCoast\Pipeline;

use SouthCoast\Pipeline\Interfaces\Pipe;
use SouthCoast\Pipeline\Interfaces\Blueprint;
use Exception;
use Closure;

/**
 * undocumented class
 */
class Pipeline
{
    protected $pipes = [];
    protected $blueprint = null;
    protected $checker = null;
    protected $payload = null;
    protected $returnHandler = null;

    public function __construct(Blueprint $blueprint = null)
    {
        $this->blueprint = $blueprint;
    }

    public function isBuildOnBlueprint()
    {
        return $this->blueprint !== null;
    }

    public function through($pipes)
    {
        foreach ($pipes as $pipe) {
            $this->pipe($pipe);
        }

        return $this;
    }

    public function pipes()
    {
        if ($this->isBuildOnBlueprint()) {
            return $this->blueprint->pipes();
        }

        foreach ($this->pipes as $pipe) {
            yield $pipe;
        }
    }

    public function pipe($pipe)
    {
        switch (true) {
            case is_string($pipe):
                if (!class_exists($pipe)) {
                    throw new Exception('Pipe class doesn\'t exists!', 1);
                }

                if (!in_array(Pipe::class, class_implements($pipe))) {
                    throw new Exception('Pipe object is not implementing ' . Pipe::class . '!', 1);
                }
                break;

            default:
                if (!$pipe instanceof Closure) {
                    throw new Exception('Pipe is not callable!', 1);
                }
                break;
        }

        $this->pipes[] = $pipe;

        return $this;
    }

    public function send($payload)
    {
        $this->payload = $payload;

        return $this;
    }

    public function checks(callable $checker)
    {
        $this->checker = $checker;

        return $this;
    }

    public function returnMethod(callable $method)
    {
        return $this->returns($method);
    }

    public function passesChecker($payload)
    {
        if (is_null($this->checker) && is_null($this->blueprint)) {
            return true;
        }

        if ($this->blueprint->hasCheck()) {
            return $this->blueprint->check($payload) === true;
        }

        return @call_user_func_array($this->checker, $payload) === true;
    }

    public function run($payload = null)
    {
        if ($payload !== null) {
            $this->send($payload);
        }

        $payload = $this->payload;

        foreach ($this->blueprint->pipes() ?? $this->pipes() as $pipe) {
            $response = call_user_func(is_string($pipe) ? new $pipe : $pipe, $payload);

            if (!is_null($response)) {
                $payload = $response;
            }

            if (!$this->passesChecker($payload)) {
                break;
            }
        }

        return $this->returnHandler($payload);
    }

    protected function returnHandler($payload)
    {
        if ($this->returnHandler !== null) {
            return @call_user_func($this->returnHandler, $payload);
        }


        if ($this->blueprint !== null && $this->blueprint->hasSeal()) {
            return $this->blueprint->seal($payload);
        }

        return $payload;
    }

    public function returns(callable $handler)
    {
        $this->returnHandler = $handler;

        return $this;
    }

    public static function open(Blueprint $blueprint = null)
    {
        return new static($blueprint);
    }
}
