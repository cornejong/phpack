<?php

namespace SouthCoast\Pipeline;

use SouthCoast\Pipeline\Pipeline;

class BlindPipeline extends Pipeline
{
    protected $shouldThrow = true;

    public function run(...$payload)
    {
        if ($payload !== []) {
            $this->send(...$payload);
        }

        foreach ($this->pipes() as $pipe) {
            try {
                call_user_func_array(is_string($pipe) ? new $pipe : $pipe, $this->payload);
            } catch (\Throwable $th) {
                if ($this->shouldThrow()) {
                    throw $th;
                } else {
                    continue;
                }
            }
        }

        return true;
    }

    public function shouldThrow(bool $should = null)
    {
        if (is_null($should)) {
            return $this->shouldThrow;
        }

        return $this->shouldThrow = $should;
    }
}
