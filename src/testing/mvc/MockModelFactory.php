<?php

namespace ntentan\dev\testing\mvc;

use Mockery;
use ntentan\mvc\MvcModelFactory;
use ntentan\nibii\RecordWrapper;
use ntentan\nibii\relationships\RelationshipType;
use Psr\Container\ContainerInterface;

class MockModelFactory extends MvcModelFactory
{
    private array $mockCollection = [];

    public function __construct()
    {
        parent::__construct("test", Mockery::mock(ContainerInterface::class));
    }

    public function createModel(string $name, RelationshipType $context): RecordWrapper
    {
        if (!isset($this->mockCollection[$name])) {
            throw new \Exception("No mock found for model: " . $name);
        }
        $model = array_shift($this->mockCollection[$name])->getMock();
        $model->initialize();
        return $model;
    }

    public function addMock(string $name): ModelMockCollector
    {
        $collector = new ModelMockCollector();
        if (!isset($this->mockCollection[$name])) {
            $this->mockCollection[$name] = [];
        }
        $this->mockCollection[$name][] = $collector;
        return $collector;
    }
}
