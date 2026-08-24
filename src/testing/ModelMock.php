<?php

namespace ntentan\dev\testing;

use ntentan\atiaa\DbContext;
use ntentan\kaikai\backends\VolatileCache;
use ntentan\kaikai\Cache;
use ntentan\mvc\MvcModelFactory;
use ntentan\nibii\factories\DefaultModelFactory;
use ntentan\nibii\factories\DefaultValidatorFactory;
use ntentan\nibii\interfaces\ModelFactoryInterface;
use ntentan\nibii\ORMContext;

class ModelMock
{
    private static ModelFactoryInterface $modelFactory;

    private static function getModelFactory(ModelFactories $name): ModelFactoryInterface
    {
        return match ($name) {
            ModelFactories::DEFAULT => new DefaultModelFactory(),
            ModelFactories::MVC => new TestingModelFactory(TestContext::getNamespace())
        };
    }

    public static function initialize(ModelFactories|ModelFactoryInterface|null $modelFactory=null): void
    {
        self::$modelFactory = (
            is_a($modelFactory, ModelFactories::class)
                ? self::getModelFactory($modelFactory)
                : $modelFactory
            ) ?? new DefaultModelFactory();
        $driverAdapterFactory = new StubDriverAdapterFactory();
        $validatorFactory = new DefaultValidatorFactory();
        $cache = new Cache(new VolatileCache());

        DbContext::initialize(new StubDriverFactory());
        ORMContext::initialize(self::$modelFactory, $driverAdapterFactory, $validatorFactory, $cache);
    }

    public static function staticCall(string $className, mixed $withInstance = null): mixed
    {
        if ($withInstance) {
            self::$modelFactory->registerCall($className, $withInstance);
            return $withInstance;
        }
        if (!is_a(self::$modelFactory, MvcModelFactory::class)) {
            trigger_error("Cannot call static methods on non MvcModelFactories");
        }
        $mock = \Mockery::mock($className);
        self::$modelFactory->registerCall($className, $mock);
        return $mock;
    }
}
