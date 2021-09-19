<?php


namespace SouthCoast\Pipeline\Interfaces;

interface Pipe
{
    public function __invoke($payload);
}
