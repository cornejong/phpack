<?php

namespace SouthCoast\Storage;

use SouthCoast\Helpers\ArrayHelper;

class Locker
{
    protected $data = [];

    public function __construct($data = null)
    {
        if ($data === null) {
            return $this;
        }

        return $this->load($data);
    }

    public function load($data)
    {
        if (!is_array($data) && !is_object($data)) {
            throw new \Exception('Provided data must be Array or Object! Provider: ' . gettype($data), 1);
        }
        
        $this->data = ArrayHelper::flatten(ArrayHelper::sanitize($data));
        return $this;
    }

    public function exists(string $identifier) : bool
    {
        return isset($this->data[$identifier]);
    }

    public function add(string $identifier, $value) : bool
    {
        $this->data[$identifier] = $value;

        return $this->data[$identifier] === $value;
    }

    public function get(string $identifier, $ifNotValue = null)
    {
        if (!$this->exists($identifier)) {
            return $ifNotValue;
        }

        return ArrayHelper::get($identifier, $this->data) ?? $ifNotValue;
    }

    public function getAll()
    {
        return $this->data;
    }

    public function remove(string $identifier)
    {
        unset($this->data[$identifier]);
    }

    public function find(string $query, $ifNotValue = null)
    {
        return ArrayHelper::searchByQuery($query, $this->$data, $found) ? $found : $ifNotValue;
    }
}
