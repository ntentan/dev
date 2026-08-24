<?php

namespace ntentan\dev\testing;

use _PHPStan_5adafcbb8\Nette\Neon\Exception;
use ntentan\mvc\MvcModelFactory;
use ntentan\nibii\RecordWrapper;

class TestingModelFactory extends MvcModelFactory
{
    private array $models = [];
    public function loadRecordWrapper(string $name)
    {
        if (isset($this->models[$name])) {
            return array_pop($this->models[$name]);
        }
        throw new \Exception("Not expecting a call to $name.");
    }

    public function registerCall(string $name, RecordWrapper $model)
    {
        if (!isset($this->models[$name])) {
            $this->models[$name] = [];
        }
        $this->models[$name][] = $model;
    }
}