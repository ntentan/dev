<?php
namespace ntentan\dev\testing;

use ntentan\nibii\interfaces\ModelFactoryInterface;
use ntentan\nibii\RecordWrapper;
use ntentan\nibii\relationships\RelationshipType;

class StubModelFactory implements ModelFactoryInterface
{

    public function createModel(string $name, RelationshipType $context): RecordWrapper
    {
        // TODO: Implement createModel() method.
    }

    public function getModelTable(RecordWrapper $instance): string
    {
        // TODO: Implement getModelTable() method.
    }

    public function getClassName(string $model): string
    {
        // TODO: Implement getClassName() method.
    }

    public function getJunctionClassName(string $classA, string $classB): string
    {
        // TODO: Implement getJunctionClassName() method.
    }
}
