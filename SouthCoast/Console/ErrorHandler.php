<?php

namespace SouthCoast\Console;

use SouthCoast\Console\Format;
use SouthCoast\Console\Console;
use SouthCoast\Console\Abstracted\Command;
use ErrorException;

class ErrorHandler
{
    const SEVERITY = [
        1 => 'E_ERROR',
        2 => 'E_WARNING',
        4 => 'E_PARSE',
        8 => 'E_NOTICE',
        16 => 'E_CORE_ERROR',
        32 => 'E_CORE_WARNING',
        64 => 'E_COMPILE_ERROR',
        128 => 'E_COMPILE_WARNING',
        256 => 'E_USER_ERROR',
        512 => 'E_USER_WARNING',
        1024 => 'E_USER_NOTICE',
        2048 => 'E_STRICT',
        4096 => 'E_RECOVERABLE_ERROR',
        8192 => 'E_DEPRECATED',
        16384 => 'E_USER_DEPRECATED',
    ];

    /**
     * @var mixed
     */
    private static $log_to_file = false;
    /**
     * @var string
     */
    private static $log_directory = '';

    /**
     * @var string
     */
    protected static $application_root = null;


    protected static $errorHeader = "  ___ _ __ _ __ ___  _ __ 
 / _ \ '__| '__/ _ \| '__|
|  __/ |  | | | (_) | |   
 \___|_|  |_|  \___/|_|   
";

    protected $headers = [
        [
            ' ___ ___ ___ ___ ___ ',
            '| -_|  _|  _| . |  _|',
            '|___|_| |_| |___|_|  '
        ], [
            
        ]
    ];

    public static function register()
    {
        //set_exception_handler(__CLASS__ . '::ExceptionHandler');
        //set_error_handler(__CLASS__ . '::ErrorHandler');
    }

    /**
     * @param $callback
     */
    public static function registerCustomErrorHandler($callback)
    {
        # code...
    }

    /**
     * @param $callback
     */
    public static function registerErrorLogger($callback)
    {
        # code...
    }

    /**
     * @param $callback
     */
    public static function registerErrorService($callback)
    {
        # code...
    }

    /**
     * @param string $directory
     */
    public static function setApplicationRoot(string $directory)
    {
        self::$application_root = $directory;
    }

    public static function applicationRoot(): string
    {
        return self::$application_root ?? app_root ?? '';
    }

    /**
     * @param \Throwable $th
     */
    public static function ExceptionHandler(\Throwable $th)
    {
        if (self::$log_to_file) {
            self::logExceptionToFile($th);
        }

        self::paint($th);

        die(Command::FAILED);
        
        return;

        $error_header = Format::bold(' == ERROR == ' . ($th instanceof \ErrorException ? ErrorHandler::SEVERITY[$th->getCode()] : get_class($th)) . ' == ');

        Console::log(Console::EOL);
        Console::log(Color::red($error_header) . ' ' . $th->getMessage());
        Console::log('Trace:');

        list($scriptPath) = get_included_files();

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
        die($th->getCode());
    }

    public static function paint($th)
    {
        /* Clear the screen but keep the content */
        Console::newLine(Console::rows());
        /* Rest the cursor to home (0,0) */
        Console::cursor()->home();
        
        // $header = Color::red(Format::leftPad(Console::columns(), ''), true) . PHP_EOL;
        $header = "" .
            ($th instanceof \ErrorException ? 'ErrorException : ' . ErrorHandler::SEVERITY[$th->getCode()] : 'Exception: ' . get_class($th));
    
        Console::newLine();
        Console::log(Color::red(str_pad('', Console::columns() / 2, '*')));
        Console::log(Color::red(str_pad(self::$errorHeader, Console::columns()), false));
        
        
        Console::log($header);
        Console::newLine();

        Console::log('> ' . Format::bold($th->getMessage()));
        Console::newLine();

        $traces = $th->getTrace();
        // array_shift($trace); // The current function
        $origin = array_shift($traces);

        $errorLines = self::getErrorLineFromSource($origin['file'], $origin['line'], 3);
        foreach ($errorLines as $number => $line) {
            Console::log($number === $origin['line'] ? Format::text("" . $number . " " . $line, 'red', null, 'underline') : "" . $number . " " . $line);
        }
        
        Console::newLine();
        Console::log(Format::italic('In ' . $origin['file'] . ' on line ' . $origin['line']));
        Console::newLine();

        Console::log('Trace:');

        foreach ($traces as $index => $trace) {
            $line1 = implode(' ', [
                '>',
                $trace['class'],
                $trace['type'],
                $trace['function'] . '(' . implode(', ', $trace['args']) . ')'
            ]);

            Console::log($line1);
            Console::log('  On line ' . $trace['line'] . ' in ' . $trace['file']);
            Console::newLine();
        }


        // Console::logVar(\debug_backtrace()[0]);
        // Console::logVar($th->getTrace());

        Console::log(Color::red(str_pad('', Console::columns() / 2, '*')));
    }

    public static function getLines($file)
    {
        $f = fopen($file, 'r');

        try {
            $number = 0;
            while ($line = fgets($f)) {
                $number++;
                yield $number => $line;
            }
        } finally {
            fclose($f);
        }
    }

    public static function getErrorLineFromSource(string $file, int $line, int $context = 1)
    {
        $lineCount = 1 + (2 * $context);
        $startLine = $line - $context;
        $endLine = $startLine + $lineCount;

        $tmp = [];

        $currentLine = 1;
        foreach (self::getLines($file) as $currentLine => $line) {
            if ($currentLine >= $endLine) {
                break;
            }

            if ($currentLine >= $startLine) {
                $value = str_replace("\n", ' ', $line);
                $tmp[$currentLine] = $value;
            }
        }


        /*         while (($line = fgets($handle)) !== false || $currentLine < $endLine) {
                    if ($currentLine >= $startLine) {
                        $value = str_replace("\n", ' ', $line);
                        $tmp[$currentLine] = $value;
                    }

                    $currentLine++;
                } */

        /* $currentLine = $startLine;
        while ($currentLine < $endLine) {
            $value = str_replace("\n", '', $lines[$currentLine -1]);
            $tmp[$currentLine] = $value;
            $currentLine++;
        } */

        return $tmp;
    }

    /**
     * @param \Throwable $th
     */
    public static function logExceptionToFile(\Throwable $th)
    {
        # code...
    }

    /**
     * @param $err_severity
     * @param $err_msg
     * @param $err_file
     * @param $err_line
     * @param array $err_context
     */
    public static function ErrorHandler($err_severity, $err_msg, $err_file, $err_line, array $err_context = null)
    {
        if (0 === error_reporting()) {
            return false;
        }

        try {
            switch ($err_severity) {
                case E_ERROR:
                    throw new ErrorException($err_msg, E_ERROR, $err_severity, $err_file, $err_line);
                case E_WARNING:
                    throw new WarningException($err_msg, E_WARNING, $err_severity, $err_file, $err_line);
                case E_PARSE:
                    throw new ParseException($err_msg, E_PARSE, $err_severity, $err_file, $err_line);
                case E_NOTICE:
                    throw new NoticeException($err_msg, E_NOTICE, $err_severity, $err_file, $err_line);
                case E_CORE_ERROR:
                    throw new CoreErrorException($err_msg, E_CORE_ERROR, $err_severity, $err_file, $err_line);
                case E_CORE_WARNING:
                    throw new CoreWarningException($err_msg, E_CORE_WARNING, $err_severity, $err_file, $err_line);
                case E_COMPILE_ERROR:
                    throw new CompileErrorException($err_msg, E_COMPILE_ERROR, $err_severity, $err_file, $err_line);
                case E_COMPILE_WARNING:
                    throw new CoreWarningException($err_msg, E_COMPILE_WARNING, $err_severity, $err_file, $err_line);
                case E_USER_ERROR:
                    throw new UserErrorException($err_msg, E_USER_ERROR, $err_severity, $err_file, $err_line);
                case E_USER_WARNING:
                    throw new UserWarningException($err_msg, E_USER_WARNING, $err_severity, $err_file, $err_line);
                case E_USER_NOTICE:
                    throw new UserNoticeException($err_msg, E_USER_NOTICE, $err_severity, $err_file, $err_line);
                case E_STRICT:
                    throw new StrictException($err_msg, E_STRICT, $err_severity, $err_file, $err_line);
                case E_RECOVERABLE_ERROR:
                    throw new RecoverableErrorException($err_msg, E_RECOVERABLE_ERROR, $err_severity, $err_file, $err_line);
                case E_DEPRECATED:
                    throw new DeprecatedException($err_msg, E_DEPRECATED, $err_severity, $err_file, $err_line);
                case E_USER_DEPRECATED:
                    throw new UserDeprecatedException($err_msg, E_USER_DEPRECATED, $err_severity, $err_file, $err_line);
            }
        } catch (\Throwable $th) {
            self::ExceptionHandler($th);
        }
    }
}

class WarningException extends ErrorException
{
}
class ParseException extends ErrorException
{
}
class NoticeException extends ErrorException
{
}
class CoreErrorException extends ErrorException
{
}
class CoreWarningException extends ErrorException
{
}
class CompileErrorException extends ErrorException
{
}
class CompileWarningException extends ErrorException
{
}
class UserErrorException extends ErrorException
{
}
class UserWarningException extends ErrorException
{
}
class UserNoticeException extends ErrorException
{
}
class StrictException extends ErrorException
{
}
class RecoverableErrorException extends ErrorException
{
}
class DeprecatedException extends ErrorException
{
}
class UserDeprecatedException extends ErrorException
{
}
