<?php

namespace ntentan\dev\testing\mvc;

use ntentan\dev\testing\nibii\RecordWrapperMocker;

class MvcMocker
{
    private static $modelFactory;
    public static function initialize(): void
    {
        self::$modelFactory = new MockModelFactory();
        RecordWrapperMocker::initialize(self::$modelFactory);
    }

    public static function createModel(string $className): ModelMockCollector
    {
        return self::$modelFactory->addMock($className);
    }

    public static function wrap(string $class, array $data): Model
    {
        $model = self::$modelFactory->getModel($class);
        $model->setData($data);
        return $model;
    }
}
