<?php declare(ticks=1);

namespace SouthCoast\Console\Abstracted;

use SouthCoast\Pipeline\MiddlewarePipeline;
use SouthCoast\Console\Console;
use SouthCoast\Console\App;
use App\App as SWApp;

abstract class Command
{
    const SUCCESS = 0;
    const FAILED = 1;

    const PATH_SEPARATOR_EXPRESSION = ' ';
    const PATH_SEPARATOR_OR_NOT_EXPRESSION = '( |)';
    const VARIABLE_MATCH_SET = '[^\/\s\<\>\*\$\#\@\!\{\}\[\]\||\"\'\;\:\~\`\±\§\\\.\,]';
    const REQUIRED_VARIABLE_EXPRESSION = '( ([^\s]+?))';
    const OPTIONAL_VARIABLE_EXPRESSION = '( ([^\s]+?)|)';

    const MATCHING_PATTERN_CLOSER = self::PATH_SEPARATOR_OR_NOT_EXPRESSION . '$/';
    const MATCHING_PATTERN_OPENER = '/^';

    const ACTION_MULTI_PART_CONTROLLER = 'ACTION_MULTI_PART_CONTROLLER';
    const COMMAND_ACTION_CONTROLLER = 'controller';
    const COMMAND_ACTION_ALIAS = 'alias';

    protected $environmentVariables = [];
    protected $arguments = [];

    protected $middleware = [];

    public $hidden = false;

    public function __invoke()
    {
        if (\method_exists($this, 'boot')) {
            $this->boot();
        }

        return $this->execute(...array_values($this->arguments));
    }

    public function __destruct()
    {
        if (\method_exists($this, 'shutdown')) {
            $this->shutdown();
        }
    }

    /**
     * Routing Components
     */

    public function register()
    {
        CommandMap::add($this);
    }

    public function export()
    {
        $this->matchingPattern = $this->buildMatchingPattern();
        
        return [$this->matchingPattern => static::class];
    }


    /**
     * Build the regex for route matching
     *
     * @return void
     */
    public function buildMatchingPattern()
    {
        $patterns = [
            /* Match optional variables */
            '/( \{[^\s]*?\?})/' => self::OPTIONAL_VARIABLE_EXPRESSION,
            /* Match required variables */
            '/( \{[^\/]*?\})/' => self::REQUIRED_VARIABLE_EXPRESSION,
        ];

        $pattern = preg_replace(array_keys($patterns), array_values($patterns), $this->accessor);
        
        return self::MATCHING_PATTERN_OPENER . $pattern . self::MATCHING_PATTERN_CLOSER;
    }

    /**
     * Runs the defined middleware
     *
     * @param mixed $response       The response from the halting middleware
     * @return boolean              true if no middleware returned a value
     */
    public function passesMiddleware(&$response) : bool
    {
        $response = null;

        $pipeline = new MiddlewarePipeline(array_merge(\App\App::get()->getGlobalMiddleware(), $this->middleware));
        
        if (!$pipeline->run()) {
            $response= $pipeline->getResult();
            return false;
        }

        return true;
    }

    public function getAccessorVariableLabels()
    {
        // /(\{(.+?|.+?\?)\})/
        $pattern = '/\{(.*?)\}/';
        $labels = [];

        $pattern = preg_match_all($pattern, $this->accessor, $matches);

        foreach ($matches[1] as $match) {
            $labels[] = trim(rtrim($match, '?'));
        }

        return $labels;
    }

    public function makeEqualLength(array $array_one, array $array_two, $padValue = null) : array
    {
        $count_one = count($array_one);
        $count_two = count($array_two);

        return [
            array_pad($array_one, $count_two, $padValue),
            array_pad($array_two, $count_one, $padValue)
        ];
    }

    public function loadCommandArguments(array $arguments)
    {
        foreach ($arguments as $index => &$argument) {
            $argument = trim($argument);
            
            if (empty($argument)) {
                unset($arguments[$index]);
            }
        }

        $arguments = array_values(array_unique($arguments));
        list($arguments, $labels) = $this->makeEqualLength($arguments, $this->getAccessorVariableLabels());

        $tmp = [];

        foreach ($arguments as $index => $argument) {
            /* Store the values without a label in the additional sub array */
            if (is_null($labels[$index])) {
                $tmp['_additional'][] = $argument;
            } else {
                /* Store it under it's label */
                $tmp[$labels[$index]] = $argument;
            }
        }

        $this->flags = Console::$flags;
        if(isset($this->flags['v']) && $this->flags['v']) {
            Console::verbose(true);
        }

        return $this->arguments = $tmp;
    }
}
