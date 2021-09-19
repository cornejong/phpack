<?php

namespace App\Modules;

use SouthCoast\Helpers\Str;
use SouthCoast\Helpers\InputValidator;

class Project extends InputValidator
{
    public $root;
    public $neverMinify = [];

    public $definition = [
        'accessor' => ['required' => true, 'allowEmpty' => false],
        'main' => ['required' => false, 'allowEmpty' => false, 'default' => 'main.php'],
        
        'bootstrapFile' => ['required' => false, 'allowEmpty' => true, 'default' => 'build/bootstrap.php'],
        'buildDirectory' => ['required' => false, 'allowEmpty' => true, 'default' => 'build'],
        'makeLogLocation' => ['required' => false, 'allowEmpty' => true, 'default' => 'build/log.json'],
        'defaultTarget' =>  ['required' => false, 'allowEmpty' => true, 'default' => 'debug'],

        'compress' => ['required' => false, 'allowEmpty' => true, 'default' => null],
        'checkSyntax' => ['required' => false, 'allowEmpty' => true, 'default' => true],
        'ignorable' => ['required' => false, 'allowEmpty' => true],
        'noMinify' => ['required' => false, 'allowEmpty' => true, 'default' => []],
    ];

    public $alwaysIgnore = [
        '.git',
        'build',
        'pack.php'
    ];

    public function __construct(string $root)
    {
        $this->root = $root;
    }

    public function getRoot()
    {
        return $this->root;
    }

    public function getAccessor()
    {
        return $this['accessor'];
    }

    public function getBuildDirectory(string $target = null)
    {
        return $this->root . '/' . $this['buildDirectory'] . ($target !== null
            ? '/' . $target
            : ''
        );
    }

    public function getBuildLog()
    {
        return $this->root . '/' . $this['makeLogLocation'];
    }

    public function getIgnorable()
    {
        return array_unique(array_filter(array_merge($this->alwaysIgnore, [$this['accessor']], $this['ignorable'] ?? [])));
    }

    public function shouldIgnore(string $file)
    {
        $file = str_replace($this->getRoot() . '/', '', $file);
        
        foreach($this->getIgnorable() as $ignore) {
            if(Str::startsWith($ignore, $file)) {
                return true;
            }
        }

        return false;
    }

    public function compresses()
    {
        return !empty($this['compress']);
    }

    public function getCompression()
    {
        return $this['compress'] ?? null;
    }

    public function getBootstrapRelativePath()
    {
        return $this['bootstrapFile'];
    }

    public function getBootstrapAbsolutePath()
    {
        return $this->root . '/' . $this['bootstrapFile'];
    }

    public function getNoMinify()
    {
        return array_unique(array_filter(array_merge($this->neverMinify, $this['noMinify'])));
    }

    public function shouldMinify(string $file)
    {
        foreach($this->getNoMinify() as $noMinify) {
            if(Str::startsWith($noMinify, $file)) {
                return false;
            }
        }

        return true;
    }
}