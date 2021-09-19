<?php

namespace App\Helpers;

/**
 * Main task is to locate the base directory of the project.
 * indicated by the {template identifier}.base or templo.base as a general identifier
 */
class Locator
{
    public static $loopCounter = 0;
    public static $loopLimit = 200;
    
    /**
     * finds the first instance of the templo.base file
     *
     * @param string $filename
     * @param string|null $current
     * @return string|null
     */
    public static function find(string $filename, $current = null)
    {
        self::$loopCounter++;

        if ($current === '/') {
            return null;
        }

        if (is_null($current)) {
            /* If not provided, set it to the current working directory */
            $current = getcwd();
        }
                
        if (file_exists($current . '/' . $filename)) {
            return $current;
        }

        $current = array_filter(explode('/', $current));

        array_pop($current);


        if (self::$loopCounter <= self::$loopLimit) {
            return self::find($filename, '/' . implode('/', $current));
        }
        
        return null;
    }
}
