<?php

namespace SouthCoast\Helpers;

use SouthCoast\Traits\ArrayAccess as ArrayAccessTrait;
use SouthCoast\KickStart\Env;
use SouthCoast\Helpers\Str;
use SouthCoast\Helpers\Expression;
use ArrayAccess;

class InputValidator implements ArrayAccess
{
    use ArrayAccessTrait;
    
    public $definition = [];

    public $invalid = [];

    public $input = [];

    public $castToScalarType = true;

    protected $containerPointer = 'input';

    public function __construct(array $definition = null, array $input = null)
    {
        $this->input($input);
        $this->definition($definition);
    }

    public function tryCastToScalarType(bool $yes)
    {
        $this->castToScalarType = $yes;
    }

    public function definition(array $definition = null)
    {
        if (!is_null($definition)) {
            $this->definition = $definition;
        }

        return $this->definition;
    }

    public function input(array $input = null)
    {
        if (!is_null($input)) {
            $this->input = $input;
        }

        return $this->input;
    }

    public function validate(array $input = null)
    {
        if (!is_null($input)) {
            $this->input($input);
        }

        foreach ($this->definition as $field => $rules) {
            if(isset($rules['empty'])) {
                $rules['allowEmpty'] = $rules['empty'];
            }

            /* Dismiss non required and missing items */
            if(!($rules['required'] ?? true) && !isset($this->input[$field])) {
                if(isset($rules['default'])) {
                    $this->validated[$field] = $rules['default'];
                }
                continue;
            }

            /* Check if it was required */
            if (($rules['required'] ?? false) && !isset($this->input[$field])) {
                $this->invalid['missing'][] = $field;
                continue;
            }

            if (($rules['allowEmpty'] ?? true) === false && empty($this->input[$field])) {
                $this->invalid['empty'][] = $field;
                continue;
            }

            /* Check if it matches the pattern */
            if (isset($rules['pattern']) && !Expression::match($rules['pattern'], $this->input[$field])) {
                $this->invalid['notMatchingPattern'][] = $field;
                continue;
            }

            if (($rules['allowEmpty'] ?? true) && empty($this->input[$field]) && isset($rules['default'])) {
                $this->input[$field] = $rules['default'];
            }

            if (isset($this->input[$field]) && $this->castToScalarType && is_string($this->input[$field])) {
                $this->input[$field] = Str::getRealType($this->input[$field]);
            }

            $this->validated[$field] = $this->input[$field];
        }

        if (empty($this->invalid)) {
            /* Switch the array access data container pointer to the validated data */
            $this->containerPointer = 'validated';
            return true;
        }

        return false;
    }

    /**
     * validates the input. if failed throws InputValidationException
     *
     * @throws InputValidationException
     * @param array|null $input
     * @return void
     */
    public function validateOrThrow(array $input = null) : void
    {
        if (!$this->validate($input)) {
            $this->throwValidationErrors();
        }
    }

    /**
     * throws the current validation errors
     *
     * @throws InputValidationException
     * @param string|null $domain
     * @return void
     */
    public function throwValidationErrors(string $domain = null) : void
    {
        foreach ($this->invalid as $type => $parameters) {
            foreach ($parameters as $parameter) {
                $failed[$parameter]['issues'][] = $type;
                $message = 'Failed the' . ($domain ? ' ' . $domain : '') . ' input validation! "' . $parameter . '" is "' . $type . '".';

                if ($type === 'notMatchingPattern') {
                    $message .= ' Pattern: ' . $this->definition[$parameter]['pattern'];
                }

                throw new InputValidationException($message);
            }
        }
    }

    public function validationErrorAsArray(string $domain = null) : array
    {
        $failed = [];

        foreach ($this->invalid as $type => $parameters) {
            foreach ($parameters as $parameter) {
                $tmp = [
                    'name' => $parameter,
                    'issue' => $type,
                ];
                
                if($type === 'notMatchingPattern') {
                   //$failed['patterns'][$parameter] = $this->definition[$parameter]['pattern'];
                    $tmp['pattern'] = $this->definition[$parameter]['pattern'];
                }
                
                $failed[] = $tmp;
            }
        }

        if($domain) {
            $failed = [Str::camelize($domain) => $failed];
        }

        return $failed;
    }
}
