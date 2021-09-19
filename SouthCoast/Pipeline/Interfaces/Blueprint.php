<?php


namespace SouthCoast\Pipeline\Interfaces;

interface Blueprint
{
    public function pipes();
    public function hasCheck() : bool;
    public function hasSeal() : bool;
}
