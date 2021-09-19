<?php

namespace SouthCoast\Pipeline;

use SouthCoast\Pipeline\Pipeline;

class MiddlewarePipeline extends Pipeline
{
    protected $result = null;
    
    public function __construct(array $pipes)
    {
        foreach ($pipes as $pipe) {
            try {
                $this->pipe($pipe);
            } catch (\Throwable $th) {
                throw $th;
            }
        }
    }

    public function run($payload = null)
    {
        foreach ($this->pipes() as $pipe) {
            $result = @call_user_func(is_string($pipe) ? new $pipe : $pipe, $payload);
            /* Check if the middleware returned anything */
            if (!is_null($result)) {
                $this->result = $result;
                return false;
            }
        }

        return true;
    }

    public function getResult()
    {
        return $this->result;
    }
}
