<?php

namespace SouthCoast\Console;

class Format
{
    const FORMAT_OPENER = "\033[";
    const FORMAT_SEPARATOR = "m";
    const FORMAT_CLOSER = "\033[0m";

    public static function text($message, $color = null, $background = null, $style = null)
    {

        // Only for terminal
        if (php_sapi_name() !== "cli") {
            return $message;
        }

        // Only for linux not for windows (PowerShell)
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return $message;
        }

        // Detect custom background mode
        if (is_int($color) and $color >= 16) {
            $background = 5;
            $style = 48;
        }

        // Set default
        $color      = (! $color)          ? 'default' : $color ;
        $background = (! $background)     ? 'default' : $background ;
        $style      = (! $style)          ? 'default' : $style ;
        $code       = [];

        $textColorCodes = [
            // Label
            'default'       => 39,
            'primary'       => 34,
            'success'       => 32,
            'info'          => 36,
            'warning'       => 33,
            'danger'        => 31,

            // Colors
            'white'         => 97,
            'black'         => 30,
            'red'           => 31,
            'green'         => 32,
            'yellow'        => 33,
            'blue'          => 34,
            'magenta'       => 35,
            'cyan'          => 36,
            'gray'          => 37,

            // Light colors
            'light-gray'    => 37,
            'light-red'     => 91,
            'light-green'   => 92,
            'light-yellow'  => 93,
            'light-blue'    => 94,
            'light-magenta' => 95,
            'light-cyan'    => 96,

            // Dark colors
            'dark-gray'     => 90,
        ];

        $backgroundColorCodes = [
            // Label
            'default'       => 39,
            'primary'       => 44,
            'success'       => 42,
            'info'          => 46,
            'warning'       => 43,
            'danger'        => 41,

            // Colors
            'white'         => 39,
            'black'         => 40,
            'red'           => 41,
            'green'         => 42,
            'yellow'        => 43,
            'blue'          => 44,
            'magenta'       => 45,
            'cyan'          => 46,
            'gray'          => 47,

            // Light colors
            'light-gray'    => 47,
            'light-red'     => 101,
            'light-green'   => 102,
            'light-yellow'  => 103,
            'light-blue'    => 104,
            'light-magenta' => 105,
            'light-cyan'    => 106,

            // Dark colors
            'dark-gray'     => 100,
        ];

        $styleCodes = [
            'default'       => 0,
            'bold'          => 1,
            'dim'           => 2,
            'italic'        => 3,
            'underline'     => 4,
            'blink'         => 5,
            'reverse'       => 7,
            'hidden'        => 8,
            'password'      => 8,
        ];

        // Set style
        if (is_int($style)) {
            $code[] = $style;
        } elseif (isset($styleCodes[ $style ])) {
            $code[] = $styleCodes[$style];
        } else {
            print_r(array_keys($backgroundColorCodes));
            die(' > Format::text(): Text style "' . $style . '" does not exist. You can only use the text styles above' . PHP_EOL);
        }

        // Set background color
        if (is_int($background)) {
            $code[] = $background;
        } elseif (isset($backgroundColorCodes[ $background ])) {
            $code[] = $backgroundColorCodes[$background];
        } else {
            print_r(array_keys($backgroundColorCodes));
            die(' > Format::text(): Background color "' . $background . '" does not exist. You can only use the background colors above' . PHP_EOL);
        }

        // Set text color
        if (is_int($color)) {
            $code[] = $color;
        } elseif (isset($textColorCodes[ $color ])) {
            $code[] = $textColorCodes[$color];
        } else {
            print_r(array_keys($textColorCodes));
            die(' > terminal_style(): Text color "' . $color . '" does not exist. You can only use the following text colors' . PHP_EOL);
        }

        // Set background
        return "\e[" . implode(';', $code) . "m" . $message . "\e[0m";
    }

    /**
     * returns a bold string
     *
     * @param string $string
     * @return string
     */
    public static function bold(string $string): string
    {
        return self::FORMAT_OPENER . '1m' . $string . self::FORMAT_CLOSER;
    }

    /**
     * returns an underlined string
     *
     * @param string $string
     * @return string
     */
    public static function underline(string $string): string
    {
        return self::FORMAT_OPENER . '4m' . $string . self::FORMAT_CLOSER;
    }

    /**
     * returns an underlined string
     *
     * @param string $string
     * @return string
     */
    public static function italic(string $string): string
    {
        return self::FORMAT_OPENER . '3m' . $string . self::FORMAT_CLOSER;
    }

    /**
     * returns an underlined string
     *
     * @param string $string
     * @return string
     */
    public static function blink(string $string): string
    {
        return self::FORMAT_OPENER . '5m' . $string . self::FORMAT_CLOSER;
    }

    /**
     * returns an underlined string
     *
     * @param string $string
     * @return string
     */
    public static function hidden(string $string): string
    {
        return self::FORMAT_OPENER . '8m' . $string . self::FORMAT_CLOSER;
    }

    /**
     * returns an underlined string
     *
     * @param string $string
     * @return string
     */
    public static function invert(string $string): string
    {
        return self::FORMAT_OPENER . '7m' . $string . self::FORMAT_CLOSER;
    }

    /**
     * returns a padded string with the number of spaces provided
     *
     * @param integer $spaces
     * @param string $string
     * @return string
     */
    public static function leftPad(int $spaces, string $string): string
    {
        # Set the $space & $i variables
        $space = "";
        # Loop the amount of spaces provided in $spaces
        for ($i = 0; $i < $spaces; $i++) {
            # Add a space to the $space variable
            $space .= " ";
        }
        # Return the space + the string
        return $space . $string;
    }

    /**
     * Returns a centered formatted string based on the number of columns
     *
     * @param integer $columns
     * @param string $string
     * @return string
     */
    public static function center(int $columns, string $string): string
    {
        # Subtract the string length from the number of columns
        $remain = $columns - strlen($string);
        # Check if the remainder is an even number
        if (Number::isEven($remain)) {
            # If so, Return the string with a left pad of half the remainder
            return (Format::leftPad(($remain / 2), $string));
        } else {
            # If not, subtract 1 from the remainder
            # and return the string with a left pad of half the remainder
            return (Format::leftPad((($remain - 1) / 2), $string));
        }
    }

    public static function right(string $string) : string
    {
        return Format::leftPad($columns - strlen($string), $string);
    }
}
