<?php

namespace SouthCoast\Pipeline;

use SouthCoast\Pipeline\Pipeline;

class VerificationPipeline extends Pipeline
{
    public function run(...$payload)
    {
        if ($payload !== []) {
            $this->send(...$payload);
        }

        foreach ($this->pipes() as $pipe) {
            if (!@call_user_func_array(is_string($pipe) ? new $pipe : $pipe, $this->payload)) {
                return false;
            }
        }

        return true;
    }
}
