<?php

/*
 * File: Expression.php
 * Project: Helpers
 * File Created: Saturday, 18th April 2020
 * Author: Corné de Jong (corne@tearo.eu)
 * ------------------------------------------------------------
 * Copyright 2020 - SouthCoast
 */

namespace SouthCoast\Helpers;

class Expression
{
    public static function match($expression, $subject, &$matches = null, $offset = 0) : bool
    {
        return preg_match($expression, $subject, $matches);

        if ($matches === null) {
            return false;
        }

        if (is_string($matches[0])) {
            return true;
        }

        $matches = self::groupMatches($matches);

        return true;
    }

    public static function matchAll($expression, $subject, &$matches = null, $offset = 0) : bool
    {
        return preg_match_all($expression, $subject, $matches);

        /* if ($matches === null) {
            $matches = [];
            return false;
        }

        if (is_string($matches[0])) {
            $matches = [];
            return true;
        } */

        // $matches = self::groupMatches($matches);

        /* if (count($matches) === 1) {
            $matches = $matches[0];
        } */

        return $result;
    }

    public static function matchAllGroup($expression, $subject, &$matches = null, $offset = 0)
    {
        return preg_match_all($expression, $subject, $matches, PREG_SET_ORDER);
    }

    public static function matchGroup(string $expression, $subject, &$matches = null, $offset = 0)
    {
        return preg_match($expression, $subject, $matches, PREG_SET_ORDER);
    }



    protected static function groupMatches(array $matches)
    {
        $result = [];

        foreach ($matches[0] as $i => $none) {
            foreach ($matches as $matchIndex => $matchValue) {
                $result[$i][$matchIndex] = $matchValue;
            }
        }

        return $result;
    }
}
