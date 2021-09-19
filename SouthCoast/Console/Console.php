<?php declare(ticks=1);

namespace SouthCoast\Console;

use SouthCoast\Helpers\StringHelper;
use SouthCoast\Helpers\Expression;
use SouthCoast\Console\Cursor;
use SouthCoast\Console\Console;

class Console
{
    const EOL = PHP_EOL;

    /**
     * @var array
     */
    protected static $config = [];

    /**
     * @var mixed
     */
    protected static $environment_arguments_map = null;
    /**
     * @var mixed
     */
    protected static $env = false;

    public static $flags = [];

    public static $io = null;
    public static $cursor = null;
    public static $color = null;

    public static $verbose = false;
    public static $buffer = [];
    public static $buffered = false;

    /**
     * Returns an instance of the console cursor
     *
     * @return Cursor
     */
    public static function cursor() : Cursor
    {
        if (!self::$cursor) {
            self::$cursor = new Cursor;
        }

        return self::$cursor;
    }

    public static function color()
    {
        if (!self::$color) {
            self::$color = new Color;
        }

        return self::$color;
    }

    public function out($value = null)
    {
        if (!self::$io) {
            self::$io = new IO;
        }


        return self::$io->out();
    }

    /**
         * @param $message
         * @param string $type
         * @param string $color
         */
    public static function log($message, string $type = 'message', string $color = 'default')
    {
        Console::log_to_console($message);
    }

    /**
     * @param $variable
     */
    public static function logVar($variable)
    {
        /* Start the output buffer */
        ob_start();
        /* Var dump the variable */
        var_dump($variable);
        /* Get the result and clean the buffer */
        $result = ob_get_clean();
        /* Log the result */
        Console::log_to_console($result);
    }

    /**
     * @param $message
     */
    public static function error($message)
    {
        Console::log(Color::red('✖') . '  ' . $message);
    }

    /**
     * @param $message
     */
    public static function success($message)
    {
        Console::log_to_console(Color::green('✓') . '  ' . $message);
    }

    /**
     * @param $message
     */
    public static function warning($message)
    {
        Console::log(Color::yellow('‼') . '  ' . $message);
    }

    /**
     * @param $message
     */
    public static function info($message)
    {
        Console::log(Color::blue('ⓘ') . '  ' . $message);
    }

    public function logException(\Throwable $th)
    {
        Console::error($th->getMessage());

        if (Env::isDev()) {
            foreach (explode("\n", $th->getTraceAsString()) as $index => $trace) {
                if (preg_match('/\d*\s(.*)(\(\d*\)):\s(.*)$/', $trace, $matches)) {
                    $trace = array_combine([
                    'all',
                    'path',
                    'line',
                    'method',
                ], $matches);

                    Console::log($index . ': ' . str_replace(self::applicationRoot(), '', $trace['path']) . $trace['line'] . ' -> ' . $trace['method']);
                } elseif (preg_match('/\d*\s({main})/', $trace, $matches)) {
                    $trace = array_combine(['all', 'origin'], $matches);
                    Console::log($index . ': ' . str_replace(self::applicationRoot(), '', get_included_files()[0]) . ' -> Application Entry ' . $trace['origin']);
                } else {
                    Console::log($trace);
                }
            }
        }
    }

    /**
     * @param string $message
     */
    public static function log_to_console(string $message)
    {
        if(self::$buffered) {
            self::$buffer[] = $message;
            return;
        }

        print $message . Console::EOL;
    }

    public static function buffered(callable $block)
    {
        self::bufferedLog(true);

        call_user_func($block);
        $output = self::collectBuffer();

        self::bufferedLog(false);

        return $output;
    }

    public static function bufferedLog(bool $buffered = null)
    {
        return $buffered === null
            ? self::$buffered
            : self::$buffered = $buffered;
    }

    public static function clearBuffer()
    {
        self::$buffer = [];
    }

    public static function collectBuffer()
    {
        $buffer = self::$buffer;
        self::clearBuffer();
        return $buffer;
    }

    public static function newLine(int $lines = 1)
    {
        while ($lines > 0) {
            print Console::EOL;
            $lines--;
        }
    }

    public static function moveCursorUp(int $lines = 1)
    {
        echo "\033[{$lines}A";
    }

    public static function moveCursorDown(int $lines = 1)
    {
        echo "\033[{$lines}B";
    }

    public static function moveCursorForward(int $characters = 1)
    {
        echo "\033[{$characters}C";
    }

    public static function moveCursorBack(int $characters = 1)
    {
        echo "\033[{$characters}D";
    }

    public static function moveCursorToStartOfLine()
    {
        /* Move to beginning of line above */
        echo "\033[1F";
        /* Move to beginning of line below */
        echo "\033[1E";
    }

    public static function clearLine()
    {
        echo "\033[2K";
    }

    public static function clearLineBeforeCursor()
    {
        echo "\033[1K";
    }

    public static function clearLineAfterCursor()
    {
        echo "\033[0K";
    }

    public static function clearAllBeforeCursor()
    {
        echo "\033[1J";
    }

    public static function clearAllAftersCursor()
    {
        echo "\033[0J";
    }

    public static function removeLastLine()
    {
        Console::moveCursorUp();
        Console::clearLine();
    }

    public static function replaceLastLine($message, string $type = 'message', string $color = 'default')
    {
        Console::moveCursorUp();
        Console::clearLine();
        Console::log($message, $type, $color);
    }

    public static function clear()
    {
        return system('clear');
    }

    public static function pwd()
    {
        return exec('pwd');
    }

    public static function exit(int $status = null)
    {
        exit($status ?? 0);
    }

    public static function columns()
    {
        return exec('tput cols');
    }

    public static function rows()
    {
        return exec('tput lines');
    }

    /**
     * @param string $command
     * @param array $environment
     * @param nullstring $execution_path
     * @param nullbool $logging
     * @return mixed
     */
    public static function run(string $command, array $environment = null, string $execution_path = null, bool $logging = false)
    {
        /* Create a new process */
        $process = new Process([
            'logging' => $logging,
        ]);
        /* Set the command */
        $process->setCommand($command)
        /* Set the env */
            ->setEnvironment($environment)
        /* Set the execution path */
            ->setExecutionPath($execution_path)
        /* Run the process */
            ->run();
        /* read the process data */
        $response = $process->read();
        /* Close the process */
        $process->close();
        /* unset the process */
        unset($process);
        /* Return the response */
        return $response;
    }

    /**
     * Create a symlink from the original to the new path
     *
     * @param string $original      The original file or directory
     * @param string $new           The new directory the symlink should be placed in
     */
    public static function symlink(string $original, string $new)
    {
        return Console::run('ln -s "' . $original . '" "' . $new . '"');
    }

    /**
     * @param string $directory
     */
    public static function mkdir(string $directory)
    {
        return Console::run('mkdir "' . $directory . '"');
    }

    public static function envIsProvided()
    {
        if (!Console::$env) {
            Console::loadEnv();
        }

        return !empty(self::$env);
    }

    /**
     * @param array $map
     */
    public static function setEnvMap(array $map)
    {
        self::$environment_arguments_map = $map;
        self::loadEnv();
    }

    /**
     * @param $name
     */
    public static function env($name = null, $ifNotValue = null)
    {
        if (!Console::$env) {
            Console::loadEnv();
        }

        if (is_null($name)) {
            return Console::$env;
        }

        return isset(Console::$env[$name]) ? Console::$env[$name] : $ifNotValue;
    }

    public static function envString()
    {
        if (!Console::$env) {
            Console::loadEnv();
        }

        return implode(' ', self::$env);
    }

    public static function flagIsSet(string $flag, bool $requiresAssignedValue = false)
    {
        if (!isset(self::$flags[$flag])) {
            return false;
        }

        return $requiresAssignedValue ? is_string(self::$flags[$flag]) : true;
    }

    public function flag(string $flag)
    {
        if (!isset(self::$flags[$flag])) {
            return null;
        }

        return self::$flags[$flag];
    }

    public static function parseEnvironment(array $environment)
    {
        /* Remove the file name from the array */
        array_shift($environment);
        /* Extract and parse the flags */
        self::$flags = self::extractEnvironmentFlags($environment);

        /* Check if we have some labels */
        if (empty(self::$environmentLabels)) {
            /* Nope, just return this */
            return $environment;
        }

        $tmp = [];

        foreach ($environment as $argument) {
            /* As long we have labels add them to the array */
            if (($label = array_shift(self::$environmentLabels))) {
                $tmp[$label] = $argument;
            } else {
                $tmp[] = $argument;
            }
        }

        /* If we still have labels left */
        if (!empty(self::$environmentLabels)) {
            /* Add them ass keys, with value false, to the tmp array */
            $tmp = array_merge($tmp, array_combine(self::$environmentLabels, array_fill(0, count(self::$environmentLabels), false)));
        }

        /* lets now return it */
        return $tmp;
    }

    public static function extractEnvironmentFlags(array &$input)
    {
        $flags = [];
        foreach ($input as $index => $value) {
            if (!StringHelper::startsWith('-', $value) && !StringHelper::startsWith('--', $value)) {
                continue;
            }

            $value = trim(ltrim($value, '-'));
            
            /* Check if it has an assigned value */
            if (Expression::match('/(^.*?)\=(.*?$)/', $value, $matches)) {
                $flags[$matches[1]] = $matches[2];
            } else {
                $flags[trim($value)] = true;
            }
            
            unset($input[$index]);
        }

        $input = array_values($input);

        return $flags;
    }

    public static function loadEnv()
    {
        global $argv;
        /* Store it in $env */
        self::$env = self::parseEnvironment($argv);
    }

    /**
     * @param $question
     */
    public static function ask($question = '', array $options = [])
    {
        echo Color::blue('?') . ' ' . $question;
        Cursor::save();

        if(isset($options['list'])) {
            echo(PHP_EOL);
            foreach ($options['list'] as $index => $option) {
               echo '  →  [ ' . $option . ' ] ' . PHP_EOL;
            }
            Cursor::restore();
        }

        /* Read the user Input and add the question */
        $line = readline(); 
        /* Add the line to the history */
        readline_add_history($line);

        Cursor::restore();
        Console::log(Color::blue($line));

        if (isset($options['format']) && is_callable($options['format'])) {
            $line = $options['format']($line);
            Cursor::restore();
            Console::log(Color::blue($line));
        }
        

        if (isset($options['validate']) && is_callable($options['validate']) && !$options['validate']($line)) {
            Cursor::restore();
            Console::log(Color::red($line));
            Console::error('Invalid input');
            return self::ask($question, $options);
        }

        Cursor::forget();
        /* return the line */
        return $line;
    }

    public static function setAutoCompleteOptions(array $options)
    {
        readline_completion_function(function ($input, $index) use ($options) {
            foreach ($options as $i => $option) {
                if (!StringHelper::startsWith(strtolower($input), strtolower($option))) {
                    unset($options[$i]);
                }

                return $options;
            }
        });
    }

    /**
     * @param $questions
     */
    public static function get(...$questions)
    {
        if (count($questions) === 1 && is_array($questions[0])) {
            $questions = $questions[0];
        }

        $answers = [];

        foreach ($questions as $index => $question) {
            $answers[$index]['question'] = $question;
            $answers[$index]['answer'] = Console::ask($question . ': ');
        }

        return count($answers) === 1 ? array_shift($answers)['answer'] : $answers;
    }

    public static function cwd()
    {
        return getcwd();
    }

    public static function verbose(bool $verbose = null)
    {
        return $verbose === null
            ? self::$verbose
            : self::$verbose = $verbose;
    }

    public static function startLoader(bool $start = true)
    {
        if(!$start) {
            return;
        }
        
        register_tick_function(function() {
            Console::loaderTickCallback();
        });
    }

    public static function stopLoader()
    {
        unregister_tick_function(function() {
            Console::loaderTickCallback();
        });
        // Cursor::moveToStartOfLineAbove();
        echo ' ';
    }

    public static function loaderTickCallback()
    {
        Cursor::moveToStartOfLineAbove();
            echo '-' . PHP_EOL;

            return;
            
        Console::log('');
        while(true) {
            Cursor::moveToStartOfLineAbove();
            echo '-' . PHP_EOL;
            yield;

            Cursor::moveToStartOfLineAbove();
            echo '\\' . PHP_EOL;
            yield;

            Cursor::moveToStartOfLineAbove();
            echo '|' . PHP_EOL;
            yield;

            Cursor::moveToStartOfLineAbove();
            echo '/' . PHP_EOL;
            yield;
        }
    }
}
